<?php

namespace App\Services\AI;

use App\DTOs\AIDTO;
use App\Repositories\AgendaRepository;
use App\Repositories\ChamadaRepository;
use App\Repositories\ComunicadoRepository;
use App\Repositories\ProvaRepository;
use App\Repositories\TarefaRepository;
use App\Repositories\UtilsAlunoRepository;
use App\Repositories\UtilsResponsavelRepository;
use App\Validators\AIValidator;
use App\Utils\Logger;

/**
 * AIService - Orquestrador principal do sistema de IA
 * 
 * Responsável por:
 * - Receber e validar perguntas do usuário
 * - Detectar intenções a partir do texto da pergunta (híbrido: Gemini + fallback por keywords)
 * - Buscar dados relevantes de múltiplas fontes (tarefas, provas, notas, etc)
 * - Construir prompts otimizados para o Gemini
 * - Gerar respostas contextualizadas e formatadas
 * - Manter contexto de conversa para suportar follow-ups
 */
class AIService
{
    // Validação e processamento do pedido do usuário
    private AIValidator $validator;
    
    // Componentes principais da pipeline de IA
    private IntentDetector $intentDetector;      // Detecta a intenção do usuário
    private FollowUpDetector $followUpDetector;   // Verifica se é uma pergunta de follow-up
    private ContextManager $contextManager;       // Gerencia histórico de conversa
    private UserContextBuilder $userContextBuilder; // Constrói contexto do usuário atual
    private PromptBuilder $promptBuilder;         // Monta prompts para Gemini
    private GeminiClient $geminiClient;          // Cliente de API Gemini
    private AnswerFormatter $answerFormatter;    // Formata respostas para apresentação

    // Repositórios para acesso aos dados do sistema
    private TarefaRepository $tarefaRepository;
    private ProvaRepository $provaRepository;
    private ComunicadoRepository $comunicadoRepository;
    private AgendaRepository $agendaRepository;
    private ChamadaRepository $chamadaRepository;
    private UtilsAlunoRepository $utilsAlunoRepository;      // Dados específicos do aluno
    private UtilsResponsavelRepository $utilsResponsavelRepository; // Dados do responsável

    public function __construct($conn)
    {
        $this->validator = new AIValidator();
        $this->intentDetector = new IntentDetector();
        $this->followUpDetector = new FollowUpDetector();
        $this->contextManager = new ContextManager();
        $this->userContextBuilder = new UserContextBuilder($conn);
        $this->promptBuilder = new PromptBuilder();
        $this->geminiClient = new GeminiClient();
        $this->answerFormatter = new AnswerFormatter();

        $this->tarefaRepository = new TarefaRepository($conn);
        $this->provaRepository = new ProvaRepository($conn);
        $this->agendaRepository = new AgendaRepository($conn);
        $this->comunicadoRepository = new ComunicadoRepository($conn);
        $this->chamadaRepository = new ChamadaRepository($conn);
        $this->utilsAlunoRepository = new UtilsAlunoRepository($conn);
        $this->utilsResponsavelRepository = new UtilsResponsavelRepository($conn);
    }

    /**
     * Processa uma pergunta do usuário e retorna uma resposta IA
     * 
     * Fluxo:
     * 1. Valida a pergunta
     * 2. Recupera contexto de conversa anterior (para follow-ups)
     * 3. Detecta intenção (ex: consultar_tarefas, consultar_agenda)
     * 4. Busca dados relevantes da intenção detectada
     * 5. Gera resposta usando Gemini (ou fallback se indisponível)
     * 6. Salva contexto para próxima interação
     */
    public function chat(AIDTO $dto): array
    {
        try {
            // Validação inicial da pergunta
            $validacao = $this->validator->validateQuestion($dto);

            if (!$validacao['success']) {
                return $validacao;
            }

            // Recupera contexto da conversa anterior (se houver)
            $conversationContext = $this->contextManager->getConversationContext();
            $authData = $this->contextManager->getAuthData();
            $userContext = $this->userContextBuilder->build($authData);

            // Verifica se é um follow-up (pergunta relacionada à anterior)
            $isFollowUp = $this->followUpDetector->isFollowUp(
                $dto->pergunta,
                $conversationContext['last_intent']
            );

            // Detecta a intenção da pergunta (híbrido: Gemini + keywords)
            $intentResult = $this->intentDetector->detect($dto->pergunta);

            if (!$intentResult['success']) {
                return $intentResult;
            }

            $intentData = $intentResult['intent_data'];

            // Se for follow-up da mesma intenção, usa dados cached
            if (
                $isFollowUp &&
                $intentData['intent'] === $conversationContext['last_intent'] &&
                !empty($conversationContext['last_data'])
            ) {
                $dados = [
                    'success' => true,
                    'data' => $conversationContext['last_data']
                ];
            } else {
                // Busca os dados específicos da intenção detectada
                $dados = $this->fetchDataByIntent($intentData, $userContext);

                if (!$dados['success']) {
                    return $dados;
                }
            }

            // Gera a resposta usando o Gemini (ou fallback)
            $answer = $this->generateAnswer(
                $dto->pergunta,
                $intentData,
                $dados['data'],
                $userContext,
                $conversationContext['last_response'],
                $conversationContext['last_intent'],
                $conversationContext['last_data']
            );

            if ($answer['success']) {
                $this->contextManager->saveConversationContext(
                    $intentData['intent'],
                    $dados['data'],
                    $answer['message']
                );
            }

            return $answer;
        } catch (\Throwable $e) {
            Logger::error('AIService::chat - ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Erro ao processar pergunta.',
                'status' => 500
            ];
        }
    }

    /**
     * Roteia para o método correto de busca de dados baseado na intenção detectada
     * 
     * Suporta intenções:
     * - consultar_tarefas: Busca tarefas
     * - consultar_provas: Busca provas agendadas
     * - consultar_notas: Busca notas(filtro por papel: aluno vs responsável)
     * - consultar_advertencias: Busca advertências (filtro por papel)
     * - consultar_agenda: Busca agenda/horários das aulas
     * - consultar_chamada: Busca frequência/presença
     * - consultar_comunicados: Busca comunicados gerais
     */
    private function fetchDataByIntent(array $intentData, array $userContext): array
    {
        $intent = $intentData['intent'] ?? 'desconhecido';

        return match ($intent) {
            'consultar_tarefas' => $this->fetchTarefas($intentData, $userContext),
            'consultar_provas' => $this->fetchProvas($intentData, $userContext),
            'consultar_notas' => $this->fetchNotas($intentData, $userContext),
            'consultar_advertencias' => $this->fetchAdvertencias($intentData, $userContext),
            'consultar_agenda' => $this->fetchAgenda($intentData, $userContext),
            'consultar_chamada' => $this->fetchChamada($intentData, $userContext),
            'consultar_comunicados' => $this->fetchComunicados($intentData, $userContext),
            default => [
                'success' => true,
                'data' => [
                    'intent' => 'desconhecido',
                    'items' => []
                ],
                'status' => 200
            ]
        };
    }

    /**
     * Busca todas as tarefas ativas
     * Tarefas são globais - não há filtro por aluno
     */
    private function fetchTarefas(array $intentData, array $userContext): array
    {
        try {
            // Tarefas são globais para todos os alunos (sem filtro por aluno)
            $items = $this->tarefaRepository->findAll();

            return [
                'success' => true,
                'data' => [
                    'intent' => 'consultar_tarefas',
                    'materia' => $intentData['materia'] ?? null,
                    'periodo' => $intentData['periodo'] ?? null,
                    'items' => $items
                ],
                'status' => 200
            ];
        } catch (\Throwable $e) {
            Logger::error("AIService - fetchTarefas error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao buscar tarefas.',
                'status' => 500
            ];
        }
    }

    /**
     * Busca provas agendadas
     * Provas são globais - não há filtro por aluno
     */
    private function fetchProvas(array $intentData, array $userContext): array
    {
        try {
            // Provas são globais para todos os alunos (sem filtro por aluno)
            $items = $this->provaRepository->findAll();

            return [
                'success' => true,
                'data' => [
                    'intent' => 'consultar_provas',
                    'materia' => $intentData['materia'] ?? null,
                    'periodo' => $intentData['periodo'] ?? null,
                    'items' => $items
                ],
                'status' => 200
            ];
        } catch (\Throwable $e) {
            Logger::error("AIService - fetchProvas error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao buscar provas.',
                'status' => 500
            ];
        }
    }

    /**
     * Busca notas do usuário
     * Filtra por papel: aluno recebe suas notas, responsável recebe notas do seu filho
     */
    private function fetchNotas(array $intentData, array $userContext): array
    {
        try {
            $role = $userContext['role'] ?? null;
            $userId = $userContext['user_id'] ?? null;

            if (!$userId) {
                return [
                    'success' => false,
                    'message' => 'Usuário não identificado.',
                    'status' => 401
                ];
            }

            // Papel do usuário determina a fonte de dados
            if ($role === 'responsavel') {
                $items = $this->utilsResponsavelRepository->getNotasFilho($userId);
            } else {
                $items = $this->utilsAlunoRepository->getNotas($userId);
            }

            return [
                'success' => true,
                'data' => [
                    'intent' => 'consultar_notas',
                    'materia' => $intentData['materia'] ?? null,
                    'periodo' => $intentData['periodo'] ?? null,
                    'items' => $items
                ],
                'status' => 200
            ];
        } catch (\Throwable $e) {
            Logger::error("AIService - fetchNotas error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao buscar notas.',
                'status' => 500
            ];
        }
    }

    /**
     * Busca advertências/disciplinary records
     * Filtra por papel: aluno recebe suas advertências, responsável recebe do seu filho
     */
    private function fetchAdvertencias(array $intentData, array $userContext): array
    {
        try {
            $role = $userContext['role'] ?? null;
            
            // Se responsável, usa student_id; caso contrário uses user_id
            // If responsável, use student_id; otherwise use user_id
            $targetUserId = ($role === 'responsavel')
                ? $userContext['student_id'] ?? null
                : $userContext['user_id'] ?? null;

            if (!$targetUserId) {
                return [
                    'success' => false,
                    'message' => 'Usuário não identificado.',
                    'status' => 401
                ];
            }

            if ($role === 'responsavel') {
                $items = $this->utilsResponsavelRepository->getAdvertenciasFilho($userContext['user_id']);
            } else {
                $items = $this->utilsAlunoRepository->getAdvertencias($targetUserId);
            }

            return [
                'success' => true,
                'data' => [
                    'intent' => 'consultar_advertencias',
                    'items' => $items
                ],
                'status' => 200
            ];
        } catch (\Throwable $e) {
            Logger::error("AIService - fetchAdvertencias error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao buscar advertências.',
                'status' => 500
            ];
        }
    }

    /**
     * Busca comunicados gerais
     * Comunicados são globais - não há filtro por aluno
     */
    private function fetchComunicados(array $intentData, array $userContext): array
    {
        try {
            $items = $this->comunicadoRepository->findAll();

            return [
                'success' => true,
                'data' => [
                    'intent' => 'consultar_comunicados',
                    'materia' => $intentData['materia'] ?? null,
                    'periodo' => $intentData['periodo'] ?? null,
                    'items' => $items
                ],
                'status' => 200
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Erro ao buscar comunicados.',
                'status' => 500
            ];
        }
    }

    /**
     * Busca agenda/horários das aulas
     * Agenda é global - todas as aulas para todos os alunos
     */
    private function fetchAgenda(array $intentData, array $userContext): array
    {
        try {
            $items = $this->agendaRepository->findAll();

            // Detectar dia específico na pergunta (você terá que passar a pergunta como parâmetro)
            // Por enquanto, retorna todos os itens
            return [
                'success' => true,
                'data' => [
                    'intent' => 'consultar_agenda',
                    'items' => $items
                ],
                'status' => 200
            ];
        } catch (\Throwable $e) {
            Logger::error("AIService - fetchAgenda error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao buscar agenda.',
                'status' => 500
            ];
        }
    }

    /**
     * Busca frequência/presença (chamada)
     * Filtra por papel: aluno recebe sua frequência, responsável recebe do seu filho
     */
    private function fetchChamada(array $intentData, array $userContext): array
    {
        try {
            $role = $userContext['role'] ?? null;
            $userId = $userContext['user_id'] ?? null;

            if (!$userId) {
                return [
                    'success' => false,
                    'message' => 'Usuário não identificado.',
                    'status' => 401
                ];
            }

            // Papel do usuário determina a fonte de dados
            if ($role === 'responsavel') {
                $items = $this->utilsResponsavelRepository->getFrequenciasFilho($userId);
            } else {
                $items = $this->utilsAlunoRepository->getFrequencias($userId);
            }

            return [
                'success' => true,
                'data' => [
                    'intent' => 'consultar_chamada',
                    'items' => $items
                ],
                'status' => 200
            ];
        } catch (\Throwable $e) {
            Logger::error("AIService - fetchChamada error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao buscar frequência.',
                'status' => 500
            ];
        }
    }

    /**
     * Gera resposta IA usando Gemini API
     * 
     * Fluxo:
     * 1. Verifica se Gemini está configurado (API key presente)
     * 2. Se não, retorna resposta formatada com fallback (keywords)
     * 3. Se sim, constrói prompt otimizado e envia para Gemini
     * 4. Trata falhas de API e retorna fallback automaticamente
     * 
     * Parâmetros:
     * - pergunta: A pergunta original do usuário
     * - intentData: Intenção detectada + contexto de intenção
     * - dados: Dados já buscados (tarefas, provas, etc)
     * - userContext: Contexto do usuário (papel, ID, nome)
     * - previousResponse: Resposta anterior (para manter contexto)
     * - lastIntent/lastData: Dados do último pedido
     */
    private function generateAnswer(
        string $pergunta,
        array $intentData,
        array $dados,
        array $userContext,
        ?string $previousResponse,
        ?string $lastIntent = null,
        ?array $lastData = null
    ): array {
        // Se Gemini não está configurado, usa fallback com keywords
        if (!$this->geminiClient->isConfigured()) {
            return [
                'success' => true,
                'message' => $this->answerFormatter->fallback($intentData, $dados, $userContext),
                'intent' => $intentData['intent'] ?? 'desconhecido',
                'status' => 200,
                'fallback' => true
            ];
        }

        // Constrói o payload para enviar ao Gemini
        $payload = $this->promptBuilder->buildAnswerPayload(
            $pergunta,
            $intentData,
            $dados,
            $userContext,
            $previousResponse,
            $lastIntent,
            $lastData
        );

        // Envia o payload para o Gemini e aguarda resposta
        $response = $this->geminiClient->generate($payload);

        // Se Gemini falhar, retorna fallback
        if (!$response['success']) {
            return [
                'success' => true,
                'message' => $this->answerFormatter->fallback($intentData, $dados, $userContext),
                'intent' => $intentData['intent'] ?? 'desconhecido',
                'status' => 200,
                'fallback' => true
            ];
        }

        // Extrai o texto da resposta do Gemini
        $texto = $response['data']['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Se resposta vazia, retorna fallback
        if ($texto === '') {
            return [
                'success' => true,
                'message' => $this->answerFormatter->fallback($intentData, $dados, $userContext),
                'intent' => $intentData['intent'] ?? 'desconhecido',
                'status' => 200,
                'fallback' => true
            ];
        }

        // Retorna resposta do Gemini sucesso
        return [
            'success' => true,
            'message' => trim($texto),
            'intent' => $intentData['intent'] ?? 'desconhecido',
            'status' => 200
        ];
    }

    /**
     * Processa a criação de uma tarefa via IA
     * 
     * Fluxo:
     * 1. Extrai dados (titulo, data_entrega, descricao) da pergunta
     * 2. Se faltar dados obrigatórios, pergunta ao usuário
     * 3. Se tiver tudo, cria a tarefa no BD
     * 4. Retorna mensagem de sucesso/erro
     */
    private function handleCreateTarefa(string $pergunta): array
    {
        try {
            // 1️⃣ Extrai dados da pergunta usando IA
            $extractionResult = $this->tarefaExtractor->extract($pergunta);

            if (!$extractionResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Erro ao processar sua solicitação.',
                    'status' => 400
                ];
            }

            $data = $extractionResult['data'];
            $missingFields = $extractionResult['missing_fields'] ?? [];

            // 2️⃣ Verifica se faltam campos obrigatórios
            if (!empty($missingFields)) {
                $fieldNames = match ($missingFields) {
                    ['titulo'] => 'o título da tarefa',
                    ['data_entrega'] => 'a data de entrega',
                    ['titulo', 'data_entrega'] => 'o título e a data de entrega',
                    default => implode(' e ', $missingFields)
                };
                
                return [
                    'success' => true,
                    'message' => "Entendi que você quer criar uma tarefa. Para finalizar, por favor informe {$fieldNames}.",
                    'intent' => 'criar_tarefa',
                    'status' => 200,
                    'waiting_for' => $missingFields
                ];
            }

            // 3️⃣ Se temos título e data_entrega, cria a tarefa
            $tarefaDTO = new \App\DTOs\TarefaDTO([
                'titulo' => $data['titulo'],
                'descricao' => $data['descricao'] ?? '',
                'data_entrega' => $data['data_entrega']
            ]);

            // Valida DTO
            $validator = new \App\Validators\TarefaValidator();
            $validacao = $validator->validateCreate($tarefaDTO);

            if (!$validacao['success']) {
                return [
                    'success' => false,
                    'message' => $validacao['message'],
                    'status' => 400
                ];
            }

            // Cria objeto Tarefa
            $tarefa = new \App\Models\Tarefa(
                $tarefaDTO->titulo,
                $tarefaDTO->descricao,
                $tarefaDTO->data_entrega
            );

            // Insere no BD
            $criou = $this->tarefaRepository->create($tarefa);

            if (!$criou) {
                return [
                    'success' => false,
                    'message' => 'Erro ao criar tarefa no sistema.',
                    'status' => 500
                ];
            }

            // 4️⃣ Retorna sucesso
            return [
                'success' => true,
                'message' => "✅ Tarefa '{$tarefaDTO->titulo}' criada com sucesso! Entrega em {$tarefaDTO->data_entrega}.",
                'intent' => 'criar_tarefa',
                'status' => 201
            ];
        } catch (\Throwable $e) {
            Logger::error('AIService::handleCreateTarefa - ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Erro ao criar tarefa.',
                'status' => 500
            ];
        }
    }
}