<?php

namespace App\Services\AI;

/**
 * FollowUpDetector - Detecta se a pergunta é um follow-up da pergunta anterior
 * 
 * Um follow-up é quando o usuário faz uma pergunta relacionada à anterior,
 * explorando mais detalhes sobre o mesmo assunto.
 * 
 * Exemplos:
 * - Pergunta 1: "Quais são minhas tarefas?" (Intent: consultar_tarefas)
 * - Pergunta 2: "Qual é o prazo?" (Follow-up: sim, mesma intenção)
 * 
 * Se detectar follow-up, o sistema reutiliza dados já buscados para
 * responder mais rapidamente.
 */
class FollowUpDetector
{
    /**
     * Detecta se uma pergunta é follow-up da anterior
     * 
     * Retorna true se:
     * - Há pergunta anterior ($lastIntent != null) E
     * - A pergunta contém keywords de follow-up OU
     * - A pergunta é muito curta (< 15 caracteres)
     */
    public function isFollowUp(string $pergunta, ?string $lastIntent): bool
    {
        // Se não hay pergunta anterior, não é follow-up
        if ($lastIntent === null) {
            return false;
        }

        $texto = mb_strtolower(trim($pergunta), 'UTF-8');

        // Keywords que indicam aprofundamento sobre o mesmo tema
        $keywords = [
            'qual',            // "Qual deles?"
            'quais',           // "Quais os detalhes?"
            'quando',          // "Quando é?"
            'onde',            // "Onde?"
            'por que',         // "Por que?"
            'porquê',          // "Porquê?"
            'como',            // "Como assim?"
            'me explica',      // "Me explica mais"
            'detalha',         // "Detalha"
            'o primeiro',      // "O primeiro deles"
            'o segundo',       // "O segundo deles"
            'o último',        // "O último deles"
            'motivo',          // "Qual o motivo?"
            'razão',           // "A razão?"
            'descrição'        // "A descrição?"
        ];

        // Busca alguma keyword de follow-up
        foreach ($keywords as $keyword) {
            if (str_contains($texto, $keyword)) {
                return true;
            }
        }

        // Perguntas muito curtas geralmente são follow-ups
        // (ex: "?", "E aí?", "Qual?", "Mais?")
        return mb_strlen($texto) < 15;
    }
}