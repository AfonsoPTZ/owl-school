<?php

namespace App\Services\AI;

class PromptBuilder
{
    public function buildIntentPayload(string $pergunta): array
    {
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

        $roleInstruction = match ($role) {
            'responsavel' => "O usuário logado é um RESPONSÁVEL. Nunca trate esse usuário como aluno. Quando falar dos dados, diga 'seu filho' ou use o nome do aluno {$studentName}.",
            'professor' => "O usuário logado é um PROFESSOR. Nunca trate esse usuário como aluno.",
            default => "O usuário logado é um ALUNO. Você pode responder usando 'você'."
        };

        $followUpHint = '';
        if ($lastIntent && $lastData) {
            $followUpHint = "\n\nCONTEXTO ANTERIOR: A pergunta anterior foi sobre {$lastIntent}. Os dados anteriores podem ser úteis para responder essa pergunta sobre {$intentData['intent']}.";
            $followUpHint .= "\nPergunta anterior respondida: {$previousResponse}";
        }

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

        $contextData = [
            'pergunta' => $pergunta,
            'intent_data' => $intentData,
            'dados_do_sistema' => $dados,
            'user_context' => $userContext
        ];

        if ($lastIntent && $lastData) {
            $contextData['contexto_anterior'] = [
                'intent' => $lastIntent,
                'dados' => $lastData,
                'resposta' => $previousResponse
            ];
        }

        $contexto = json_encode($contextData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Se for agenda, formatar de forma mais legível
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
                'temperature' => 0.2,
                'maxOutputTokens' => 500
            ]
        ];
    }

    private function formatAgendaParaGemini(array $items): string
    {
        if (empty($items)) {
            return "Não há aulas agendadas";
        }

        $agendasPorDia = [];
        $diasOrdenados = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];

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