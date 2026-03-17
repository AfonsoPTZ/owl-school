<?php

namespace App\Services;

class AIIntentService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
        $this->model = $_ENV['GEMINI_MODEL'] ?? 'gemini-2.5-flash';
    }

    public function classifyIntent(string $pergunta): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'GEMINI_API_KEY não configurada no .env.',
                'status'  => 500
            ];
        }

        $systemInstruction = <<<TXT
Você é um classificador de intenção do sistema escolar Owl School.

Sua tarefa é analisar a pergunta do usuário e retornar SOMENTE um JSON válido.

Intenções permitidas:
- consultar_tarefas
- consultar_provas
- consultar_notas
- consultar_comunicados
- desconhecido

Regras:
- Se o usuário falar de tarefa, atividade, exercício, dever, lição de casa, usar consultar_tarefas.
- Se falar de prova, avaliação, teste, usar consultar_provas.
- Se falar de nota, resultado, desempenho em prova, usar consultar_notas.
- Se falar de aviso, recado, comunicado, usar consultar_comunicados.
- Se não der para saber, usar desconhecido.

Campos do JSON:
- intent: string
- materia: string|null
- periodo: string|null

Sobre "periodo":
- Use "esta_semana" se o usuário disser "essa semana", "nesta semana".
- Use "hoje" se o usuário disser "hoje".
- Use null se não houver período claro.

Sobre "materia":
- Extraia a disciplina se estiver explícita, como matemática, português, história etc.
- Caso contrário, null.

Não escreva explicação.
Não escreva markdown.
Somente JSON.
TXT;

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
                        ['text' => $pergunta]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0,
                'maxOutputTokens' => 120,
                'response_mime_type' => 'application/json',
                'response_schema' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'intent' => [
                            'type' => 'STRING',
                            'enum' => [
                                'consultar_tarefas',
                                'consultar_provas',
                                'consultar_notas',
                                'consultar_comunicados',
                                'desconhecido'
                            ]
                        ],
                        'materia' => [
                            'type' => 'STRING',
                            'nullable' => true
                        ],
                        'periodo' => [
                            'type' => 'STRING',
                            'nullable' => true
                        ]
                    ],
                    'required' => ['intent', 'materia', 'periodo']
                ]
            ]
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

        $response = $this->postJson($url, $payload);

        if (!$response['success']) {
            return $response;
        }

        $data = $response['data'];
        $jsonText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($jsonText)) {
            return [
                'success' => false,
                'message' => 'O Gemini não retornou a classificação da intenção.',
                'status'  => 500
            ];
        }

        $intentData = json_decode($jsonText, true);

        if (!is_array($intentData)) {
            return [
                'success' => false,
                'message' => 'O JSON de intenção veio inválido.',
                'status'  => 500
            ];
        }

        return [
            'success' => true,
            'intent_data' => [
                'intent'  => $intentData['intent'] ?? 'desconhecido',
                'materia' => $intentData['materia'] ?? null,
                'periodo' => $intentData['periodo'] ?? null,
            ],
            'status' => 200
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