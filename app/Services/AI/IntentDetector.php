<?php

namespace App\Services\AI;

/**
 * IntentDetector - Detecta a intenção por trás da pergunta do usuário
 * 
 * Usa abordagem HÍBRIDA:
 * 1. Primeira tentativa: Envia pergunta para Gemini para classificação
 * 2. Fallback: Se Gemini falhar/indisponível, usa keywords predefinidas
 * 
 * Intenções reconhecidas:
 * - consultar_tarefas: Buscar tarefas/deveres
 * - consultar_provas: Buscar provas agendadas
 * - consultar_notas: Buscar notas/desempenho
 * - consultar_advertencias: Buscar advertências/ocorrências
 * - consultar_comunicados: Buscar comunicados gerais
 * - consultar_agenda: Buscar agenda/horários das aulas
 * - consultar_chamada: Buscar frequência/presença
 */
class IntentDetector
{
    private GeminiClient $geminiClient;
    private PromptBuilder $promptBuilder;

    public function __construct()
    {
        $this->geminiClient = new GeminiClient();
        $this->promptBuilder = new PromptBuilder();
    }

    /**
     * Detecta a intenção da pergunta
     * 
     * Retorna:
     * {
     *   "success": true,
     *   "intent_data": {
     *     "intent": "consultar_tarefas",
     *     "materia": null,
     *     "periodo": null
     *   },
     *   "status": 200,
     *   "fallback": true (opcional - apenas se usou fallback)
     * }
     */
    public function detect(string $pergunta): array
    {
        // Tenta usar Gemini para detecção inteligente
        if (!$this->geminiClient->isConfigured()) {
            return $this->fallback($pergunta);
        }

        // Constrói payload de detecção de intenção
        $payload = $this->promptBuilder->buildIntentPayload($pergunta);
        $response = $this->geminiClient->generate($payload);

        // Se Gemini retornar erro de rate-limit (429), usa fallback
        if (!$response['success']) {
            return $response['status'] === 429
                ? $this->fallback($pergunta)
                : $response;
        }

        // Extrai JSON da resposta
        $jsonText = $response['data']['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if ($jsonText === '') {
            return $this->fallback($pergunta);
        }

        // Parse do JSON retornado pelo Gemini
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

    /**
     * Fallback por keywords quando Gemini indisponível
     * 
     * Mapeia keywords em português para intenções específicas
     * Prioridade: primeira correspondência encontrada ganha
     */
    private function fallback(string $pergunta): array
    {
        // Normaliza texto: minúsculas e sem acentos não conforme português
        $texto = mb_strtolower(trim($pergunta), 'UTF-8');

        // Mapa de keywords → intenção
        // Cada intenção tem múltiplas variações de palavras-chave
        $map = [
            'consultar_tarefas' => ['tarefa', 'dever', 'atividade', 'lição', 'exercício'],
            'consultar_provas' => ['prova', 'teste', 'avaliação', 'exame'],
            'consultar_notas' => ['nota', 'boletim', 'resultado', 'média', 'desempenho'],
            'consultar_advertencias' => ['advertência', 'advertencias', 'ocorrência', 'repreensão'],
            'consultar_comunicados' => ['comunicado', 'recado', 'mensagem', 'aviso geral'],
            'consultar_agenda' => ['agenda', 'aula', 'horário', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'essa semana', 'próxima semana'],
            'consultar_chamada' => ['chamada', 'chamadas', 'frequência', 'frequencia', 'presença', 'presenca', 'falta', 'faltas']
        ];

        // Busca primeira intenção que tenha keyword correspondente
        $intent = 'desconhecido';

        foreach ($map as $intentName => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($texto, $keyword)) {
                    $intent = $intentName;
                    break 2;  // Sai dos dois loops
                }
            }
        }

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