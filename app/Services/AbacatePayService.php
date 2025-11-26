<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class AbacatePayService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.abacatepay.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . config('abacatepay.api_key'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'timeout' => 30,
            'verify' => true,
        ]);
    }

    /**
     * Cria uma cobrança PIX via AbacatePay
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function createPix(array $payload): array
    {
        try {
            $endpoint = 'pixQrCode/create';
            $fullUrl = $this->client->getConfig('base_uri') . $endpoint;

            Log::info('Criando PIX na AbacatePay', [
                'payload' => $payload,
                'endpoint' => $endpoint,
                'full_url' => $fullUrl,
                'api_key_prefix' => substr(config('abacatepay.api_key'), 0, 10) . '...'
            ]);

            $response = $this->client->post($endpoint, [
                'json' => $payload,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            Log::info('Resposta da AbacatePay', ['response' => $body]);

            // A resposta vem em { data: {...}, error: null }
            if (isset($body['error']) && $body['error'] !== null) {
                throw new \Exception($body['error']);
            }

            return $body['data'] ?? $body;

        } catch (GuzzleException $e) {
            $errorBody = $e->getMessage();

            if (method_exists($e, 'hasResponse') && $e->hasResponse()) {
                $errorBody = $e->getResponse()->getBody()->getContents();
            }

            Log::error('Erro ao criar PIX na AbacatePay', [
                'error' => $errorBody,
                'code' => $e->getCode(),
            ]);

            throw new \Exception("Erro ao criar PIX: {$errorBody}");
        }
    }

    /**
     * Consulta status de uma cobrança
     *
     * @param string $billingId
     * @return array
     * @throws \Exception
     */
    public function getBilling(string $billingId): array
    {
        try {
            $response = $this->client->get("billing/{$billingId}");
            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            Log::error('Erro ao consultar status da cobrança', [
                'billing_id' => $billingId,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Erro ao consultar cobrança: ' . $e->getMessage());
        }
    }

    /**
     * Cancela uma cobrança
     *
     * @param string $billingId
     * @return array
     * @throws \Exception
     */
    public function cancelBilling(string $billingId): array
    {
        try {
            $response = $this->client->delete("billing/{$billingId}");
            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            Log::error('Erro ao cancelar cobrança', [
                'billing_id' => $billingId,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Erro ao cancelar cobrança: ' . $e->getMessage());
        }
    }

    /**
     * Valida webhook do AbacatePay
     *
     * @param array $payload
     * @return bool
     */
    public function validateWebhook(array $payload): bool
    {
        // Validação básica: verificar se tem os campos essenciais
        return isset($payload['event']) && isset($payload['data']);
    }

    /**
     * Processa webhook do AbacatePay
     *
     * @param array $payload
     * @return array
     */
    public function processWebhook(array $payload): array
    {
        $event = $payload['event'] ?? null;
        $pixData = $payload['data']['pixQrCode'] ?? null;

        if (!$pixData) {
            throw new \Exception('Dados do PIX não encontrados no webhook');
        }

        return [
            'event' => $event,
            'billing_id' => $pixData['id'] ?? null,
            'status' => $pixData['status'] ?? null,
            'amount' => isset($pixData['amount']) ? $pixData['amount'] / 100 : 0,
            'external_id' => $pixData['metadata']['externalId'] ?? null,
            'metadata' => $pixData['metadata'] ?? [],
            'paid' => ($pixData['status'] ?? '') === 'PAID',
        ];
    }
}
