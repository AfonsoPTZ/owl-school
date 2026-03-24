<?php

namespace App\Services\AI;

/**
 * PromptBuilder - Constrói prompts otimizados para o Google Gemini
 * 
 * Responsável por:
 * - Criar system instructions claros para classificação de intenção
 * - Montar payloads para detecção de intenção (intent detection)
 * - Montar payloads para geração de resposta (answer generation)
 * - Incluir contexto do usuário (papel, nome, etc)
 * - Manter contexto de conversa anterior (follow-ups)
 */
class PromptBuilder
{
    /**
     * Constrói payload para detecção de intenção
     * 
     * RetornaJSON estruturado que Gemini classifica
     * Ex: { "intent": "consultar_tarefas", "materia": null, "periodo": null }
     */
    public function buildIntentPayload(string $pergunta): array
    {
        // System instruction define o classificador
        $systemInstruction = <<<TXT
Você é um classificador de intenção do sistema escolar Owl School.

Retorne SOMENTE JSON válido.

Intenções permitidas:
- consultar_tarefas
- consultar_provas
- consultar_notas
- consultar_advertencias
- consultar_comunicados
- consultar_agenda
- consultar_chamada
- desconhecido

Regras:
- tarefa, atividade, exercício, dever, lição de casa -> consultar_tarefas
- prova, avaliação, teste -> consultar_provas
- nota, boletim, resultado -> consultar_notas
- advertência, repreensão, ocorrência -> consultar_advertencias
- comunicado, recado, aviso geral -> consultar_comunicados
- agenda, aula, horário, segunda, terca, quarta, quinta, sexta, essa semana -> consultar_agenda
- chamada, frequência, presença, falta -> consultar_chamada
- se não souber -> desconhecido

Campos:
- intent
- materia
- periodo

Não escreva explicação.
Não escreva markdown.
Somente JSON.
TXT;

        return [
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
                'temperature' => 0,  // Determinístico: sempre mesma resposta
                'maxOutputTokens' => 120,  // Apenas JSON pequenino
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
                                'consultar_advertencias',
                                'consultar_comunicados',
                                'consultar_agenda',
                                'consultar_chamada',
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
    }

    /**
     * Constrói payload para geração de resposta
     * 
     * Inclui:
     * - System instruction com contexto do usuário
     * - Contexto anterior (para follow-ups)
     * - Dados buscados do sistema
     * - Instrução para não inventar informações
     */
    public function buildAnswerPayload(
        string $pergunta,
        array $intentData,
        array $dados,
        array $userContext,
        ?string $previousResponse = null,
        ?string $lastIntent = null,
        ?array $lastData = null
    ): array {
        $role = $userContext['role'] ?? 'desconhecido';
        $studentName = $userContext['student_name'] ?? null;

        // Adapta instruções conforme papel do usuário
        $roleInstruction = match ($role) {
            'responsavel' => "O usuário logado é um RESPONSÁVEL. Nunca trate esse usuário como aluno. Quando falar dos dados, diga 'seu filho' ou use o nome do aluno {$studentName}.",
            'professor' => "O usuário logado é um PROFESSOR. Nunca trate esse usuário como aluno.",
            default => "O usuário logado é um ALUNO. Você pode responder usando 'você'."
        };

        // Inclui contexto anterior se for follow-up
        $followUpHint = '';
        if ($lastIntent && $lastData) {
            $followUpHint = "\n\nCONTEXTO ANTERIOR: A pergunta anterior foi sobre {$lastIntent}. Os dados anteriores podem ser úteis para responder essa pergunta sobre {$intentData['intent']}.";
            $followUpHint .= "\nPergunta anterior respondida: {$previousResponse}";
        }

        // System instruction com regras para a resposta
        $systemInstruction = <<<TXT
Você é o assistente escolar do Owl School.

Regras obrigatórias:
- Responda em português do Brasil.
- Seja claro, curto e útil.
- Use APENAS os dados fornecidos.
- Não invente informações.
- Se não houver dados, diga isso claramente.
- Respeite o papel do usuário logado.
- {$roleInstruction}{$followUpHint}
TXT;

        // Monta contexto estruturado para Gemini
        $contextData = [
            'pergunta' => $pergunta,
            'intent_data' => $intentData,
            'dados_do_sistema' => $dados,
            'user_context' => $userContext
        ];

        // Inclui contexto anterior se disponível
        if ($lastIntent && $lastData) {
            $contextData['contexto_anterior'] = [
                'intent' => $lastIntent,
                'dados' => $lastData,
                'resposta' => $previousResponse
            ];
        }

        $contexto = json_encode($contextData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Se for agenda, formata agenda de forma legível para Gemini
        if (($intentData['intent'] ?? null) === 'consultar_agenda') {
            $agendaFormatada = $this->formatAgendaParaGemini($dados['items'] ?? []);
            $contexto = str_replace(
                '"dados_do_sistema": ' . json_encode($dados),
                '"dados_do_sistema": ' . json_encode(['intent' => 'consultar_agenda', 'agenda_formatada' => $agendaFormatada, 'items' => $dados['items'] ?? []]),
                $contexto
            );
        }

        return [
            'system_instruction' => [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => "Responda com base neste contexto:\n\n" . $contexto]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,  // Pouca variação (mais consistente)
                'maxOutputTokens' => 500  // Respostas mais formadas
            ]
        ];
    }

    /**
     * Formata agenda em texto legível para o modelo Gemini
     * Agrupa por dia da semana e formata com horários
     */
    private function formatAgendaParaGemini(array $items): string
    {
        if (empty($items)) {
            return "Não há aulas agendadas";
        }

        $agendasPorDia = [];
        $diasOrdenados = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];

        // Agrupa por dia da semana
        foreach ($items as $item) {
            $dia = strtolower($item['dia_semana'] ?? '');
            if (!$dia) continue;

            if (!isset($agendasPorDia[$dia])) {
                $agendasPorDia[$dia] = [];
            }

            $disciplina = $item['disciplina'] ?? 'Sem disciplina';
            $inicio = $item['inicio'] ?? '--:--';
            $fim = $item['fim'] ?? '--:--';

            $agendasPorDia[$dia][] = "{$disciplina} ({$inicio} - {$fim})";
        }

        // Formata em ordem cronológica
        $resultado = "AGENDA DE AULAS:\n";

        foreach ($diasOrdenados as $dia) {
            if (isset($agendasPorDia[$dia])) {
                $nomeDia = $this->traduzirDia($dia);
                $resultado .= "\n{$nomeDia}:\n";

                foreach ($agendasPorDia[$dia] as $aula) {
                    $resultado .= "  • {$aula}\n";
                }
            }
        }

        return $resultado;
    }

    /**
     * Helper: Converte dia da semana para português com acentos
     */
    private function traduzirDia(string $dia): string
    {
        return match ($dia) {
            'segunda' => 'Segunda-feira',
            'terca' => 'Terça-feira',
            'quarta' => 'Quarta-feira',
            'quinta' => 'Quinta-feira',
            'sexta' => 'Sexta-feira',
            'sabado' => 'Sábado',
            'domingo' => 'Domingo',
            default => ucfirst($dia)
        };
    }
}