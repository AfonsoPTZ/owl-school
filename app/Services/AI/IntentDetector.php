<?php

namespace App\Services\AI;

class IntentDetector
{
    private GeminiClient $geminiClient;
    private PromptBuilder $promptBuilder;

    public function __construct()
    {
        $this->geminiClient = new GeminiClient();
        $this->promptBuilder = new PromptBuilder();
    }

    public function detect(string $pergunta): array
    {
        if (!$this->geminiClient->isConfigured()) {
            return $this->fallback($pergunta);
        }

        $payload = $this->promptBuilder->buildIntentPayload($pergunta);
        $response = $this->geminiClient->generate($payload);

        if (!$response['success']) {
            return $response['status'] === 429
                ? $this->fallback($pergunta)
                : $response;
        }

        $jsonText = $response['data']['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if ($jsonText === '') {
            return $this->fallback($pergunta);
        }

        $intentData = json_decode($jsonText, true);

        if (!is_array($intentData)) {
            return $this->fallback($pergunta);
        }

        return [
            'success' => true,
            'intent_data' => [
                'intent' => $intentData['intent'] ?? 'desconhecido',
                'materia' => $intentData['materia'] ?? null,
                'periodo' => $intentData['periodo'] ?? null
            ],
            'status' => 200
        ];
    }

    private function fallback(string $pergunta): array
    {
        $texto = mb_strtolower(trim($pergunta), 'UTF-8');

        $map = [
            'consultar_tarefas' => ['tarefa', 'dever', 'atividade', 'lição', 'exercício'],
            'consultar_provas' => ['prova', 'teste', 'avaliação', 'exame'],
            'consultar_notas' => ['nota', 'boletim', 'resultado', 'média', 'desempenho'],
            'consultar_advertencias' => ['advertência', 'advertencias', 'ocorrência', 'repreensão'],
            'consultar_comunicados' => ['comunicado', 'recado', 'mensagem', 'aviso geral'],
            'consultar_agenda' => ['agenda', 'aula', 'horário', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'essa semana', 'próxima semana'],
            'consultar_chamada' => ['chamada', 'chamadas', 'frequência', 'frequencia', 'presença', 'presenca', 'falta', 'faltas']
        ];

        $intent = 'desconhecido';

        foreach ($map as $intentName => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($texto, $keyword)) {
                    $intent = $intentName;
                    break 2;
                }
            }
        }

        \App\Utils\Logger::info("IntentDetector fallback: pergunta='{$pergunta}', intent detected={$intent}");

        return [
            'success' => true,
            'intent_data' => [
                'intent' => $intent,
                'materia' => null,
                'periodo' => null
            ],
            'status' => 200,
            'fallback' => true
        ];
    }
}