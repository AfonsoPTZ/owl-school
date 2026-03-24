<?php

namespace App\Services\AI;

use App\Utils\Logger;

class GeminiClient
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
        $this->model = $_ENV['GEMINI_MODEL'] ?? 'gemini-2.5-flash';
    }

    public function isConfigured(): bool
    {
        return $this->apiKey !== '';
    }

    public function generate(array $payload): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'GEMINI_API_KEY não configurada no .env.',
                'status' => 500
            ];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

        try {
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

            if ($rawResponse === false || $curlError) {
                Logger::error('GeminiClient CURL: ' . $curlError);

                return [
                    'success' => false,
                    'message' => 'Erro ao conectar com Gemini.',
                    'status' => 500
                ];
            }

            $data = json_decode($rawResponse, true);

            if ($httpCode >= 400) {
                $apiMessage = $data['error']['message'] ?? 'Erro desconhecido na API Gemini.';
                $isQuotaError = $httpCode === 429 || stripos($apiMessage, 'quota') !== false;

                return [
                    'success' => false,
                    'message' => $isQuotaError
                        ? 'Limite de requisições do Gemini atingido.'
                        : 'Gemini retornou erro: ' . $apiMessage,
                    'status' => $isQuotaError ? 429 : $httpCode
                ];
            }

            return [
                'success' => true,
                'data' => $data,
                'status' => 200
            ];
        } catch (\Throwable $e) {
            Logger::error('GeminiClient exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Erro ao fazer requisição ao Gemini.',
                'status' => 500
            ];
        }
    }
}