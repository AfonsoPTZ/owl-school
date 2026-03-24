<?php

namespace App\Services\AI;

/**
 * AnswerFormatter - Formata respostas em texto legível para o user
 * 
 * Responsável por:
 * - Converter dados estruturados em texto natural
 * - Usar fallback quando Gemini não está disponível
 * - Formatar diferentes tipos de conteúdo (tarefas, provas, notas, etc)
 * - Adaptar resposta ao contexto do usuário (aluno vs responsável vs professor)
 */
class AnswerFormatter
{
    /**
     * Formata resposta de fallback
     * Chamado quando Gemini está indisponível
     * Roteia para o método correto baseado na intenção
     */
    public function fallback(array $intentData, array $dados, array $userContext): string
    {
        $intent = $intentData['intent'] ?? 'desconhecido';
        $items = $dados['items'] ?? [];

        return match ($intent) {
            'consultar_tarefas' => $this->formatTarefas($items, $userContext),
            'consultar_provas' => $this->formatProvas($items, $userContext),
            'consultar_notas' => $this->formatNotas($items, $userContext),
            'consultar_advertencias' => $this->formatAdvertencias($items, $userContext),
            'consultar_comunicados' => $this->formatComunicados($items, $userContext),
            'consultar_agenda' => $this->formatAgenda($items, $userContext),
            'consultar_chamada' => $this->formatChamada($items, $userContext),
            default => 'Desculpe, não consegui entender sua pergunta.'
        };
    }

    /**
     * Retorna o sujeito da resposta adaptado ao contexto do usuário
     * Ex: "você", "seu filho João", "a turma"
     */
    private function subject(array $userContext): string
    {
        $role = $userContext['role'] ?? 'aluno';
        $studentName = $userContext['student_name'] ?? null;

        return match ($role) {
            'responsavel' => $studentName ? "seu filho {$studentName}" : 'seu filho',
            'professor' => 'a turma',
            default => 'você'
        };
    }

    /**
     * Formata lista de tarefas/deveres em texto
     */
    private function formatTarefas(array $items, array $userContext): string
    {
        $subject = $this->subject($userContext);

        if (empty($items)) {
            return "{$subject} não tem tarefas no momento.";
        }

        $count = count($items);
        $resposta = ucfirst($subject) . " tem {$count} tarefa" . ($count > 1 ? 's' : '') . ":\n";

        foreach (array_slice($items, 0, 5) as $item) {
            $titulo = $item['titulo'] ?? $item['descricao'] ?? 'Sem título';
            $resposta .= "- {$titulo}\n";
        }

        if ($count > 5) {
            $resposta .= "... e mais " . ($count - 5) . ".";
        }

        return trim($resposta);
    }

    /**
     * Formata lista de provas em texto
     */
    private function formatProvas(array $items, array $userContext): string
    {
        $subject = $this->subject($userContext);

        if (empty($items)) {
            return "{$subject} não tem provas agendadas no momento.";
        }

        $count = count($items);
        $resposta = ucfirst($subject) . " tem {$count} prova" . ($count > 1 ? 's' : '') . " agendada" . ($count > 1 ? 's' : '') . ":\n";

        foreach (array_slice($items, 0, 5) as $item) {
            $titulo = $item['titulo'] ?? $item['descricao'] ?? 'Sem título';
            $data = $item['data'] ?? 'Data não informada';
            $resposta .= "- {$titulo} ({$data})\n";
        }

        if ($count > 5) {
            $resposta .= "... e mais " . ($count - 5) . ".";
        }

        return trim($resposta);
    }

    /**
     * Formata lista de notas em texto
     */
    private function formatNotas(array $items, array $userContext): string
    {
        $subject = $this->subject($userContext);

        if (empty($items)) {
            return "{$subject} ainda não tem notas registradas.";
        }

        $count = count($items);
        $resposta = ucfirst($subject) . " tem {$count} nota" . ($count > 1 ? 's' : '') . ":\n";

        foreach (array_slice($items, 0, 5) as $item) {
            $materia = $item['materia'] ?? $item['descricao'] ?? 'Sem descrição';
            $valor = $item['nota'] ?? $item['valor'] ?? 'N/A';
            $resposta .= "- {$materia}: {$valor}\n";
        }

        if ($count > 5) {
            $resposta .= "... e mais " . ($count - 5) . ".";
        }

        return trim($resposta);
    }

    /**
     * Formata lista de advertências em texto
     */
    private function formatAdvertencias(array $items, array $userContext): string
    {
        $subject = $this->subject($userContext);

        if (empty($items)) {
            return "{$subject} não tem advertências registradas.";
        }

        $count = count($items);
        $resposta = ucfirst($subject) . " tem {$count} advertência" . ($count > 1 ? 's' : '') . " registrada" . ($count > 1 ? 's' : '') . ":\n";

        foreach (array_slice($items, 0, 5) as $item) {
            $titulo = $item['titulo'] ?? 'Sem título';
            $descricao = $item['descricao'] ?? 'Sem descrição';
            $resposta .= "- {$titulo}: {$descricao}\n";
        }

        if ($count > 5) {
            $resposta .= "... e mais " . ($count - 5) . ".";
        }

        return trim($resposta);
    }

    /**
     * Formata lista de comunicados em texto
     */
    private function formatComunicados(array $items, array $userContext): string
    {
        if (empty($items)) {
            return 'Não há comunicados no momento.';
        }

        $count = count($items);
        $resposta = "Há {$count} comunicado" . ($count > 1 ? 's' : '') . ":\n";

        foreach (array_slice($items, 0, 3) as $item) {
            $titulo = $item['titulo'] ?? $item['descricao'] ?? 'Sem título';
            $resposta .= "- {$titulo}\n";
        }

        if ($count > 3) {
            $resposta .= "... e mais " . ($count - 3) . ".";
        }

        return trim($resposta);
    }

    /**
     * Formata agenda/horários de aulas em texto
     * Agrupa por dia da semana em ordem cronológica
     */
    private function formatAgenda(array $items, array $userContext): string
    {
        if (empty($items)) {
            return 'Não há aulas agendadas.';
        }

        // Agrupar por dia da semana
        $agendasPorDia = [];
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

        // Ordenar dias da semana em sequência
        $diasOrdenados = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        $diasExistentes = array_intersect($diasOrdenados, array_keys($agendasPorDia));

        if (empty($diasExistentes)) {
            return 'Não há aulas agendadas.';
        }

        $resposta = '';

        foreach ($diasExistentes as $dia) {
            if ($resposta !== '') {
                $resposta .= "\n";
            }

            $nomeDia = $this->traduzirDia($dia);
            $resposta .= "{$nomeDia}:\n";

            foreach ($agendasPorDia[$dia] as $aula) {
                $resposta .= "• {$aula}\n";
            }
        }

        return trim($resposta);
    }

    /**
     * Helper: Converte dia da semana para português com acento
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

    /**
     * Formata frequência/presença em texto
     * Mostra estatísticas + detalhes se houver poucos registros
     */
    private function formatChamada(array $items, array $userContext): string
    {
        $subject = $this->subject($userContext);

        if (empty($items)) {
            return "{$subject} não tem registros de frequência no momento.";
        }

        $count = count($items);
        $presentes = 0;
        $ausentes = 0;

        // Contabiliza presentes vs ausentes
        foreach ($items as $item) {
            $status = strtolower($item['status'] ?? '');
            if ($status === 'presente' || $status === 'presença') {
                $presentes++;
            } elseif ($status === 'ausente' || $status === 'falta') {
                $ausentes++;
            }
        }

        // Monta resposta com estatísticas
        $resposta = "Frequência de {$subject}:\n";
        $resposta .= "Total de registros: {$count}\n";
        $resposta .= "Presentes: {$presentes}\n";
        $resposta .= "Ausentes: {$ausentes}";

        // Se poucos registros, mostra detalhes
        if ($count <= 10) {
            $resposta .= "\n\nDetalhes:";
            foreach ($items as $item) {
                $data = $item['data'] ?? 'Data não informada';
                $status = $item['status'] ?? 'Status não informado';
                $resposta .= "\n- {$data}: {$status}";
            }
        }

        return trim($resposta);
    }
}