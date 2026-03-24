<?php

namespace App\Services\AI;

class AnswerFormatter
{
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

        // Ordenar dias da semana
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

    private function formatChamada(array $items, array $userContext): string
    {
        $subject = $this->subject($userContext);

        if (empty($items)) {
            return "{$subject} não tem registros de frequência no momento.";
        }

        $count = count($items);
        $presentes = 0;
        $ausentes = 0;

        foreach ($items as $item) {
            $status = strtolower($item['status'] ?? '');
            if ($status === 'presente' || $status === 'presença') {
                $presentes++;
            } elseif ($status === 'ausente' || $status === 'falta') {
                $ausentes++;
            }
        }

        $resposta = "Frequência de {$subject}:\n";
        $resposta .= "Total de registros: {$count}\n";
        $resposta .= "Presentes: {$presentes}\n";
        $resposta .= "Ausentes: {$ausentes}";

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