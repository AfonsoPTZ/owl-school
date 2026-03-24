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

class AIService
{
    private AIValidator $validator;
    private IntentDetector $intentDetector;
    private FollowUpDetector $followUpDetector;
    private ContextManager $contextManager;
    private UserContextBuilder $userContextBuilder;
    private PromptBuilder $promptBuilder;
    private GeminiClient $geminiClient;
    private AnswerFormatter $answerFormatter;

    private TarefaRepository $tarefaRepository;
    private ProvaRepository $provaRepository;
    private ComunicadoRepository $comunicadoRepository;
    private AgendaRepository $agendaRepository;
    private ChamadaRepository $chamadaRepository;
    private UtilsAlunoRepository $utilsAlunoRepository;
    private UtilsResponsavelRepository $utilsResponsavelRepository;

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

    public function chat(AIDTO $dto): array
    {
        try {
            $validacao = $this->validator->validateQuestion($dto);

            if (!$validacao['success']) {
                return $validacao;
            }

            $conversationContext = $this->contextManager->getConversationContext();
            $authData = $this->contextManager->getAuthData();
            $userContext = $this->userContextBuilder->build($authData);

            $isFollowUp = $this->followUpDetector->isFollowUp(
                $dto->pergunta,
                $conversationContext['last_intent']
            );

            $intentResult = $this->intentDetector->detect($dto->pergunta);

            if (!$intentResult['success']) {
                return $intentResult;
            }

            $intentData = $intentResult['intent_data'];

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
                $dados = $this->fetchDataByIntent($intentData, $userContext);

                if (!$dados['success']) {
                    return $dados;
                }
            }

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

    private function fetchNotas(array $intentData, array $userContext): array
    {
        try {
            $role = $userContext['role'] ?? null;
            $userId = $userContext['user_id'] ?? null;

            Logger::info("AIService - fetchNotas: role={$role}, userId={$userId}");

            if (!$userId) {
                return [
                    'success' => false,
                    'message' => 'Usuário não identificado.',
                    'status' => 401
                ];
            }

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

    private function fetchAdvertencias(array $intentData, array $userContext): array
    {
        try {
            $role = $userContext['role'] ?? null;
            
            // If responsável, use student_id; otherwise use user_id
            $targetUserId = ($role === 'responsavel')
                ? $userContext['student_id'] ?? null
                : $userContext['user_id'] ?? null;

            Logger::info("AIService - fetchAdvertencias: role={$role}, targetUserId={$targetUserId}");

            if (!$targetUserId) {
                return [
                    'success' => false,
                    'message' => 'Usuário não identificado.',
                    'status' => 401
                ];
            }

            if ($role === 'responsavel') {
                $items = $this->utilsResponsavelRepository->getAdvertenciasFilho($userContext['user_id']);
                Logger::info("AIService - fetchAdvertencias responsavel: " . count($items) . " advertências encontradas");
            } else {
                $items = $this->utilsAlunoRepository->getAdvertencias($targetUserId);
                Logger::info("AIService - fetchAdvertencias aluno: " . count($items) . " advertências encontradas");
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

    private function generateAnswer(
        string $pergunta,
        array $intentData,
        array $dados,
        array $userContext,
        ?string $previousResponse,
        ?string $lastIntent = null,
        ?array $lastData = null
    ): array {
        if (!$this->geminiClient->isConfigured()) {
            return [
                'success' => true,
                'message' => $this->answerFormatter->fallback($intentData, $dados, $userContext),
                'intent' => $intentData['intent'] ?? 'desconhecido',
                'status' => 200,
                'fallback' => true
            ];
        }

        $payload = $this->promptBuilder->buildAnswerPayload(
            $pergunta,
            $intentData,
            $dados,
            $userContext,
            $previousResponse,
            $lastIntent,
            $lastData
        );

        $response = $this->geminiClient->generate($payload);

        if (!$response['success']) {
            return [
                'success' => true,
                'message' => $this->answerFormatter->fallback($intentData, $dados, $userContext),
                'intent' => $intentData['intent'] ?? 'desconhecido',
                'status' => 200,
                'fallback' => true
            ];
        }

        $texto = $response['data']['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if ($texto === '') {
            return [
                'success' => true,
                'message' => $this->answerFormatter->fallback($intentData, $dados, $userContext),
                'intent' => $intentData['intent'] ?? 'desconhecido',
                'status' => 200,
                'fallback' => true
            ];
        }

        return [
            'success' => true,
            'message' => trim($texto),
            'intent' => $intentData['intent'] ?? 'desconhecido',
            'status' => 200
        ];
    }
}