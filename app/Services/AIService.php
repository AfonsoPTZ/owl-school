<?php

namespace App\Services;

use App\DTOs\AIDTO;
use App\Repositories\TarefaRepository;
use App\Validators\AIValidator;

// Ajuste esses imports conforme os nomes reais no teu projeto:
use App\Repositories\ProvaRepository;
use App\Repositories\ProvaNotaRepository;
use App\Repositories\ComunicadoRepository;

class AIService
{
    private AIValidator $validator;
    private AIIntentService $intentService;

    private TarefaRepository $tarefaRepository;
    private ?ProvaRepository $provaRepository = null;
    private ?ProvaNotaRepository $provaNotaRepository = null;
    private ?ComunicadoRepository $comunicadoRepository = null;

    private string $apiKey;
    private string $model;

    public function __construct($conn)
    {
        $this->validator = new AIValidator();
        $this->intentService = new AIIntentService();

        $this->tarefaRepository = new TarefaRepository($conn);

        // Ajuste conforme os repositories que você já tem
        if (class_exists(ProvaRepository::class)) {
            $this->provaRepository = new ProvaRepository($conn);
        }

        if (class_exists(ProvaNotaRepository::class)) {
            $this->provaNotaRepository = new ProvaNotaRepository($conn);
        }

        if (class_exists(ComunicadoRepository::class)) {
            $this->comunicadoRepository = new ComunicadoRepository($conn);
        }

        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
        $this->model = $_ENV['GEMINI_MODEL'] ?? 'gemini-2.5-flash';
    }

    public function chat(AIDTO $dto): array
    {
        $validacao = $this->validator->validateQuestion($dto);

        if (!$validacao['success']) {
            return $validacao;
        }

        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'GEMINI_API_KEY não configurada no .env.',
                'status'  => 500
            ];
        }

        // 1) Gemini classifica a intenção
        $intentResult = $this->intentService->classifyIntent($dto->pergunta);

        if (!$intentResult['success']) {
            return $intentResult;
        }

        $intentData = $intentResult['intent_data'];

        // 2) PHP busca os dados reais
        $dados = $this->fetchDataByIntent($intentData);

        if (!$dados['success']) {
            return $dados;
        }

        // 3) Gemini escreve a resposta final
        return $this->generateFinalAnswer(
            $dto->pergunta,
            $intentData,
            $dados['data']
        );
    }

    private function fetchDataByIntent(array $intentData): array
    {
        $intent = $intentData['intent'] ?? 'desconhecido';

        switch ($intent) {
            case 'consultar_tarefas':
                return $this->fetchTarefas($intentData);

            case 'consultar_provas':
                return $this->fetchProvas($intentData);

            case 'consultar_notas':
                return $this->fetchNotas($intentData);

            case 'consultar_comunicados':
                return $this->fetchComunicados($intentData);

            default:
                return [
                    'success' => true,
                    'data' => [
                        'intent' => 'desconhecido',
                        'items' => []
                    ],
                    'status' => 200
                ];
        }
    }

    private function fetchTarefas(array $intentData): array
    {
        // Hoje você só tem findAll().
        // Depois o ideal é criar filtros por aluno, turma, matéria e período.
        $tarefas = $this->tarefaRepository->findAll();

        return [
            'success' => true,
            'data' => [
                'intent' => 'consultar_tarefas',
                'materia' => $intentData['materia'] ?? null,
                'periodo' => $intentData['periodo'] ?? null,
                'items' => $tarefas
            ],
            'status' => 200
        ];
    }

    private function fetchProvas(array $intentData): array
    {
        if (!$this->provaRepository) {
            return [
                'success' => false,
                'message' => 'ProvaRepository não configurado ainda.',
                'status'  => 500
            ];
        }

        // Ajusta aqui conforme teu método real
        $provas = method_exists($this->provaRepository, 'findAll')
            ? $this->provaRepository->findAll()
            : [];

        return [
            'success' => true,
            'data' => [
                'intent' => 'consultar_provas',
                'materia' => $intentData['materia'] ?? null,
                'periodo' => $intentData['periodo'] ?? null,
                'items' => $provas
            ],
            'status' => 200
        ];
    }

    private function fetchNotas(array $intentData): array
    {
        if (!$this->provaNotaRepository) {
            return [
                'success' => false,
                'message' => 'ProvaNotaRepository não configurado ainda.',
                'status'  => 500
            ];
        }

        // Ajusta aqui conforme teu método real
        $notas = method_exists($this->provaNotaRepository, 'findAll')
            ? $this->provaNotaRepository->findAll()
            : [];

        return [
            'success' => true,
            'data' => [
                'intent' => 'consultar_notas',
                'materia' => $intentData['materia'] ?? null,
                'periodo' => $intentData['periodo'] ?? null,
                'items' => $notas
            ],
            'status' => 200
        ];
    }

    private function fetchComunicados(array $intentData): array
    {
        if (!$this->comunicadoRepository) {
            return [
                'success' => false,
                'message' => 'ComunicadoRepository não configurado ainda.',
                'status'  => 500
            ];
        }

        // Ajusta aqui conforme teu método real
        $comunicados = method_exists($this->comunicadoRepository, 'findAll')
            ? $this->comunicadoRepository->findAll()
            : [];

        return [
            'success' => true,
            'data' => [
                'intent' => 'consultar_comunicados',
                'materia' => $intentData['materia'] ?? null,
                'periodo' => $intentData['periodo'] ?? null,
                'items' => $comunicados
            ],
            'status' => 200
        ];
    }

    private function generateFinalAnswer(string $pergunta, array $intentData, array $dados): array
    {
        $systemInstruction = <<<TXT
Você é o assistente escolar do Owl School.

Regras:
- Responda em português do Brasil.
- Seja claro, curto e útil.
- Use APENAS os dados fornecidos.
- Se não houver dados suficientes, diga isso claramente.
- Não invente tarefas, provas, notas, datas ou comunicados.
- Se houver muitos itens, faça um resumo objetivo.
TXT;

        $contexto = json_encode([
            'pergunta_original' => $pergunta,
            'intent_data' => $intentData,
            'dados_do_sistema' => $dados
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => "Gere a resposta final para o aluno com base neste contexto:\n\n" . $contexto]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 300
            ]
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

        $response = $this->postJson($url, $payload);

        if (!$response['success']) {
            return $response;
        }

        $data = $response['data'];
        $texto = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$texto) {
            return [
                'success' => false,
                'message' => 'A resposta final do Gemini veio vazia.',
                'status'  => 500
            ];
        }

        return [
            'success' => true,
            'message' => trim($texto),
            'intent'  => $intentData['intent'] ?? 'desconhecido',
            'status'  => 200
        ];
    }

    private function postJson(string $url, array $payload): array
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . $this->apiKey
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => 30,
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($rawResponse === false || !empty($curlError)) {
            return [
                'success' => false,
                'message' => 'Erro ao conectar com o Gemini: ' . $curlError,
                'status'  => 500
            ];
        }

        $data = json_decode($rawResponse, true);

        if ($httpCode >= 400) {
            $apiMessage = $data['error']['message'] ?? 'Erro desconhecido na API Gemini.';
            return [
                'success' => false,
                'message' => 'Gemini retornou erro: ' . $apiMessage,
                'status'  => 500
            ];
        }

        return [
            'success' => true,
            'data'    => $data,
            'status'  => 200
        ];
    }
}