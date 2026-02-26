<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class XGateService
{
    private Client $client;
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('xgate.api_url', 'https://api.xgateglobal.com'), '/');

        $this->client = new Client([
            'base_uri' => $this->baseUrl . '/',
            'timeout'  => 30,
            'verify'   => true,
            'headers'  => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function isConfigured(): bool
    {
        return !empty(config('xgate.email')) && !empty(config('xgate.password'));
    }

    // -------------------------------------------------------------------------
    // AUTENTICAÇÃO
    // -------------------------------------------------------------------------

    /**
     * Faz login e retorna o token Bearer.
     * Cacheia o token por 50 minutos para não fazer login a cada requisição.
     */
    public function getToken(): string
    {
        return Cache::remember('xgate_token', 50 * 60, function () {
            try {
                $response = $this->client->post('auth/token', [
                    'json' => [
                        'email'    => config('xgate.email'),
                        'password' => config('xgate.password'),
                    ],
                ]);

                $body = json_decode($response->getBody()->getContents(), true);

                Log::info('XGate: login realizado com sucesso');

                return $body['token'];

            } catch (GuzzleException $e) {
                $errorBody = method_exists($e, 'hasResponse') && $e->hasResponse()
                    ? $e->getResponse()->getBody()->getContents()
                    : $e->getMessage();

                Log::error('XGate: erro ao fazer login', ['error' => $errorBody]);
                throw new \Exception("Erro ao autenticar na XGate: {$errorBody}");
            }
        });
    }

    /**
     * Retorna headers autenticados para uso nas requisições.
     */
    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . $this->getToken()];
    }

    // -------------------------------------------------------------------------
    // MOEDAS
    // -------------------------------------------------------------------------

    /**
     * Busca as moedas fiduciárias disponíveis na conta (ex: BRL).
     * Cacheia por 1 hora pois raramente muda.
     */
    public function getCurrencies(): array
    {
        return Cache::remember('xgate_currencies', 60 * 60, function () {
            try {
                $response = $this->client->get('deposit/company/currencies', [
                    'headers' => $this->authHeaders(),
                ]);

                return json_decode($response->getBody()->getContents(), true);

            } catch (GuzzleException $e) {
                $errorBody = method_exists($e, 'hasResponse') && $e->hasResponse()
                    ? $e->getResponse()->getBody()->getContents()
                    : $e->getMessage();

                Log::error('XGate: erro ao buscar currencies', ['error' => $errorBody]);
                throw new \Exception("Erro ao buscar moedas XGate: {$errorBody}");
            }
        });
    }

    /**
     * Retorna o objeto BRL do array de currencies.
     */
    private function getBrlCurrency(): array
    {
        $currencies = $this->getCurrencies();

        foreach ($currencies as $currency) {
            if (strtoupper($currency['name']) === 'BRL') {
                return $currency;
            }
        }

        throw new \Exception('Moeda BRL não encontrada na conta XGate');
    }

    /**
     * Busca as criptomoedas disponíveis para conversão (ex: USDT).
     * Cacheia por 1 hora.
     */
    public function getCryptocurrencies(): array
    {
        return Cache::remember('xgate_cryptocurrencies', 60 * 60, function () {
            try {
                $response = $this->client->get('deposit/company/cryptocurrencies', [
                    'headers' => $this->authHeaders(),
                ]);

                return json_decode($response->getBody()->getContents(), true);

            } catch (GuzzleException $e) {
                $errorBody = method_exists($e, 'hasResponse') && $e->hasResponse()
                    ? $e->getResponse()->getBody()->getContents()
                    : $e->getMessage();

                Log::error('XGate: erro ao buscar cryptocurrencies', ['error' => $errorBody]);
                throw new \Exception("Erro ao buscar criptomoedas XGate: {$errorBody}");
            }
        });
    }

    /**
     * Retorna o objeto USDT do array de cryptocurrencies.
     */
    private function getUsdtCryptocurrency(): array
    {
        $cryptos = $this->getCryptocurrencies();

        foreach ($cryptos as $crypto) {
            if (strtoupper($crypto['symbol']) === 'USDT') {
                return $crypto;
            }
        }

        throw new \Exception('Criptomoeda USDT não encontrada na conta XGate');
    }

    // -------------------------------------------------------------------------
    // CLIENTES
    // -------------------------------------------------------------------------

    /**
     * Cria um cliente na XGate.
     * Em caso de 409 (duplicado), retorna o _id do cliente já existente.
     */
    public function createCustomer(array $data): string
    {
        try {
            $response = $this->client->post('customer', [
                'headers' => $this->authHeaders(),
                'json'    => [
                    'name'                    => $data['name'],
                    'document'                => $data['document'],
                    'email'                   => $data['email'] ?? null,
                    'phone'                   => $data['phone'] ?? null,
                    'notValidationDuplicated' => true,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            Log::info('XGate: cliente criado', ['id' => $body['customer']['_id']]);

            return $body['customer']['_id'];

        } catch (GuzzleException $e) {
            // 409 = já existe cliente com mesmo documento ou nome
            if ($e->getCode() === 409 && method_exists($e, 'hasResponse') && $e->hasResponse()) {
                $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);

                if (!empty($errorBody['customer']['_id'])) {
                    Log::info('XGate: cliente já existia, reutilizando', ['id' => $errorBody['customer']['_id']]);
                    return $errorBody['customer']['_id'];
                }
            }

            $errorBody = method_exists($e, 'hasResponse') && $e->hasResponse()
                ? $e->getResponse()->getBody()->getContents()
                : $e->getMessage();

            Log::error('XGate: erro ao criar cliente', ['error' => $errorBody]);
            throw new \Exception("Erro ao criar cliente XGate: {$errorBody}");
        }
    }

    /**
     * Busca ou cria o cliente XGate para um usuário.
     * Salva o xgate_customer_id no User para não recriar.
     */
    public function getOrCreateCustomer(\App\Models\User $user): string
    {
        if (!empty($user->xgate_customer_id)) {
            return $user->xgate_customer_id;
        }

        $customerId = $this->createCustomer([
            'name'     => $user->name,
            'document' => $user->cpf ?? '00000000000',
            'email'    => $user->email,
            'phone'    => $user->phone ?? null,
        ]);

        $user->xgate_customer_id = $customerId;
        $user->save();

        return $customerId;
    }

    // -------------------------------------------------------------------------
    // DEPÓSITO PIX (BRL → USDT)
    // -------------------------------------------------------------------------

    /**
     * Cria pedido de depósito PIX em BRL com conversão automática para USDT.
     * Busca currency BRL e cryptocurrency USDT dinamicamente via API.
     *
     * @param  float  $amount      Valor em BRL
     * @param  string $customerId  _id XGate do cliente
     * @param  string $externalId  ID interno da transação (idempotência)
     * @return array  { id, code (copia-e-cola), status, customer_id }
     */
    public function createPixDeposit(float $amount, string $customerId, string $externalId): array
    {
        $currency       = $this->getBrlCurrency();
        $cryptocurrency = $this->getUsdtCryptocurrency();

        $payload = [
            'amount'         => $amount,
            'customerId'     => $customerId,
            'currency'       => $currency,
            'cryptocurrency' => $cryptocurrency,
            'externalId'     => $externalId,
        ];

        try {
            $response = $this->client->post('deposit', [
                'headers' => $this->authHeaders(),
                'json'    => $payload,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            Log::info('XGate: depósito criado', ['body' => $body]);

            if (isset($body['error']) && $body['error'] !== null) {
                throw new \Exception($body['error']);
            }

            $data = $body['data'] ?? $body;

            return [
                'id'          => $data['id'],
                'code'        => $data['code'],       // copia-e-cola PIX
                'status'      => $data['status'],
                'customer_id' => $data['customerId'] ?? $customerId,
            ];

        } catch (GuzzleException $e) {
            $errorBody = method_exists($e, 'hasResponse') && $e->hasResponse()
                ? $e->getResponse()->getBody()->getContents()
                : $e->getMessage();

            Log::error('XGate: erro ao criar depósito PIX', [
                'error'  => $errorBody,
                'amount' => $amount,
            ]);

            throw new \Exception("Erro ao criar PIX XGate: {$errorBody}");
        }
    }

    // -------------------------------------------------------------------------
    // STATUS / UTILITÁRIOS
    // -------------------------------------------------------------------------

    /**
     * Consulta o status de um depósito.
     */
    public function getDeposit(string $depositId): array
    {
        try {
            $response = $this->client->get("deposit/{$depositId}/details", [
                'headers' => $this->authHeaders(),
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            Log::error('XGate: erro ao consultar depósito', [
                'deposit_id' => $depositId,
                'error'      => $e->getMessage(),
            ]);
            throw new \Exception('Erro ao consultar depósito XGate: ' . $e->getMessage());
        }
    }

    /**
     * Reenvia o postback de um depósito.
     */
    public function resendWebhook(string $depositId): array
    {
        try {
            $response = $this->client->post("deposit/{$depositId}/resend/webhook", [
                'headers' => $this->authHeaders(),
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            Log::error('XGate: erro ao reenviar postback', [
                'deposit_id' => $depositId,
                'error'      => $e->getMessage(),
            ]);
            throw new \Exception('Erro ao reenviar webhook XGate: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // WEBHOOK
    // -------------------------------------------------------------------------

    /**
     * Validação do payload de webhook.
     *
     * Payload real enviado pela XGate:
     * {
     *   "id": "...",
     *   "status": "PAID",
     *   "name": "USDT",
     *   "amount": 0.19,
     *   "operation": "DEPOSIT",
     *   "customerId": "...",
     *   "externalId": "TXN-..."
     * }
     */
    public function validateWebhook(array $payload): bool
    {
        return isset($payload['id']) && isset($payload['status']) && isset($payload['operation']);
    }

    /**
     * Normaliza os dados do webhook para o formato usado no WebhookController.
     */
    public function processWebhook(array $payload): array
    {
        $status     = strtoupper($payload['status'] ?? '');
        $externalId = $payload['externalId'] ?? null;
        $depositId  = $payload['id'] ?? null;
        $amount     = (float) ($payload['amount'] ?? 0);

        return [
            'event'       => $payload['operation'] ?? 'DEPOSIT',
            'billing_id'  => $depositId,
            'status'      => $status,
            'amount'      => $amount,
            'external_id' => $externalId,
            'paid'        => $status === 'PAID',
        ];
    }
}
