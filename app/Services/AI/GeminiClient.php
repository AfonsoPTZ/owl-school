<?php

namespace App\Services\AI;

use App\Utils\Logger;

/**
 * GeminiClient - Cliente para a API do Google Gemini
 * 
 * Responsável por:
 * - Comunicar com a API REST do Google Gemini
 * - Enviar payloads estruturados
 * - Tratar respostas e erros
 * - Detectar rate-limit errors (429)
 * - Fazer log de erros para debug
 */
class GeminiClient
{
    private string $apiKey;      // Chave de API do Gemini (de GEMINI_API_KEY no .env)
    private string $model;       // Modelo Gemini a usar (ex: gemini-2.5-flash)

    public function __construct()
    {
        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
        $this->model = $_ENV['GEMINI_MODEL'] ?? 'gemini-2.5-flash';
    }

    /**
     * Verifica se a API está configurada (se tem chave de API)
     */
    public function isConfigured(): bool
    {
        return $this->apiKey !== '';
    }

    /**
     * Faz requisição para o Gemini e retorna resposta
     * 
     * Parâmetro $payload contém:
     * - system_instruction: Instrução de sistema
     * - contents: Mensagens (role + parts with text)
     * - generationConfig: Tokens, temperature, schema, etc
     * 
     * Retorna array com sucesso e dados da API
     */
    public function generate(array $payload): array
    {
        // Valida se está configurado
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'GEMINI_API_KEY não configurada no .env.',
                'status' => 500
            ];
        }

        // URL da API Gemini v1beta
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

        try {
            // Prepara requisição CURL
            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,   // Retorna resposta como string
                CURLOPT_POST => true,              // Method POST
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'x-goog-api-key: ' . $this->apiKey  // Autenticação
                ],
                CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
                CURLOPT_TIMEOUT => 30,  // 30 segundos de timeout
            ]);

            // Executa requisição
            $rawResponse = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);

            curl_close($ch);

            // Trata erros de conexão
            if ($rawResponse === false || $curlError) {
                Logger::error('GeminiClient CURL: ' . $curlError);

                return [
                    'success' => false,
                    'message' => 'Erro ao conectar com Gemini.',
                    'status' => 500
                ];
            }

            // Parse da resposta JSON
            $data = json_decode($rawResponse, true);

            // Trata erros HTTP
            if ($httpCode >= 400) {
                $apiMessage = $data['error']['message'] ?? 'Erro desconhecido na API Gemini.';
                // Detecta rate-limit (429) ou mensagens de quota
                $isQuotaError = $httpCode === 429 || stripos($apiMessage, 'quota') !== false;

                return [
                    'success' => false,
                    'message' => $isQuotaError
                        ? 'Limite de requisições do Gemini atingido.'
                        : 'Gemini retornou erro: ' . $apiMessage,
                    'status' => $isQuotaError ? 429 : $httpCode
                ];
            }

            // Sucesso: retorna dados da API
            return [
                'success' => true,
                'data' => $data,
                'status' => 200
            ];
        } catch (\Throwable $e) {
            // Trata exceções não previstas
            Logger::error('GeminiClient exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Erro ao fazer requisição ao Gemini.',
                'status' => 500
            ];
        }
    }
}