<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.asaas.sandbox')
            ? 'https://api-sandbox.asaas.com/v3'
            : 'https://api.asaas.com/v3';
        $this->apiKey = config('services.asaas.api_key');
    }

    private function asaasClient()
    {
        return Http::withHeaders([
            'access_token' => $this->apiKey,
        ])->acceptJson();
    }

    /**
     * Verifica se o serviço está configurado
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Cria ou busca um cliente no Asaas
     * @throws \Exception quando CPF/CNPJ não está disponível ou criação falha
     */
    public function getOrCreateCustomer(array $customerData): string
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Asaas nao configurado (ASAAS_API_KEY vazio).');
        }

        // Buscar cliente existente pelo CPF/CNPJ
        $cpfCnpj = preg_replace('/\D/', '', $customerData['cpfCnpj'] ?? '');

        // Asaas EXIGE CPF/CNPJ para criar cliente
        if (empty($cpfCnpj) || strlen($cpfCnpj) < 11) {
            Log::warning('Asaas: CPF/CNPJ não disponível ou inválido', [
                'cpfCnpj_length' => strlen($cpfCnpj),
                'email' => $customerData['email'] ?? 'N/A',
            ]);
            throw new \Exception('CPF/CNPJ é obrigatório para pagamento via Asaas.');
        }

        try {
            // Buscar cliente existente
            $response = $this->asaasClient()
                ->get("{$this->baseUrl}/customers", [
                    'cpfCnpj' => $cpfCnpj,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['data']) && count($data['data']) > 0) {
                    Log::info('Cliente Asaas encontrado', ['customer_id' => $data['data'][0]['id']]);
                    return $data['data'][0]['id'];
                }
            }

            // Criar novo cliente
            $customerPayload = [
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'cpfCnpj' => $cpfCnpj,
            ];

            // Adicionar telefone se disponível
            $phone = preg_replace('/\D/', '', $customerData['phone'] ?? '');
            if (!empty($phone) && strlen($phone) >= 10) {
                $customerPayload['phone'] = $phone;
            }

            $response = $this->asaasClient()
                ->asJson()
                ->post("{$this->baseUrl}/customers", $customerPayload);

            if ($response->successful()) {
                $customerId = $response->json()['id'];
                Log::info('Cliente Asaas criado', ['customer_id' => $customerId]);
                return $customerId;
            }

            $errorData = $response->json();
            $errorMessage = $errorData['errors'][0]['description'] ?? 'Erro desconhecido ao criar cliente';

            Log::error('Erro ao criar cliente no Asaas', [
                'status' => $response->status(),
                'response' => $errorData,
                'payload' => array_merge($customerPayload, ['cpfCnpj' => '***']), // Ocultar CPF no log
            ]);

            throw new \Exception("Erro ao criar cliente no Asaas: {$errorMessage}");

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Erro de conexão com Asaas', ['error' => $e->getMessage()]);
            throw new \Exception('Erro de conexão com Asaas. Tente novamente.');
        }
    }

    /**
     * Cria uma cobrança PIX no Asaas
     */
    public function createPix(array $payload): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Asaas não configurado (ASAAS_API_KEY vazio).');
        }

        // Criar ou buscar cliente (pode lançar exceção se CPF não disponível)
        $customerId = $this->getOrCreateCustomer([
            'name' => $payload['customer']['name'],
            'email' => $payload['customer']['email'],
            'cpfCnpj' => $payload['customer']['taxId'] ?? $payload['customer']['cpfCnpj'] ?? '',
            'phone' => $payload['customer']['cellphone'] ?? $payload['customer']['phone'] ?? '',
        ]);

        try {

            // Calcular data de vencimento
            $dueDate = now()->addDay()->format('Y-m-d');

            // Criar cobrança PIX
            $response = $this->asaasClient()
                ->asJson()
                ->timeout(30)
                ->post("{$this->baseUrl}/payments", [
                    'customer' => $customerId,
                    'billingType' => 'PIX',
                    'value' => $payload['amount'] / 100, // Converter de centavos para reais
                    'dueDate' => $dueDate,
                    'description' => $payload['description'] ?? 'Pagamento via PIX',
                    'externalReference' => $payload['metadata']['externalId'] ?? null,
                ]);

            if (!$response->successful()) {
                $errorData = $response->json();
                $errorMessage = $errorData['errors'][0]['description'] ?? 'Erro ao criar cobrança PIX no Asaas';

                Log::error('Erro ao criar PIX no Asaas', [
                    'status' => $response->status(),
                    'response' => $errorData,
                ]);

                throw new \Exception($errorMessage);
            }

            $paymentData = $response->json();
            $paymentId = $paymentData['id'];

            Log::info('Cobrança Asaas criada', ['payment_id' => $paymentId]);

            // Buscar QR Code PIX
            $pixResponse = $this->asaasClient()
                ->timeout(30)
                ->get("{$this->baseUrl}/payments/{$paymentId}/pixQrCode");

            if (!$pixResponse->successful()) {
                Log::error('Erro ao buscar QR Code PIX no Asaas', [
                    'payment_id' => $paymentId,
                    'status' => $pixResponse->status(),
                    'response' => $pixResponse->json(),
                ]);
                throw new \Exception('Erro ao gerar QR Code PIX no Asaas.');
            }

            $pixData = $pixResponse->json();

            Log::info('PIX criado no Asaas com sucesso', [
                'payment_id' => $paymentId,
                'has_qrcode' => !empty($pixData['encodedImage']),
            ]);

            // Retornar dados no mesmo formato que AbacatePay para compatibilidade
            return [
                'id' => $paymentId,
                'brCode' => $pixData['payload'],
                'brCodeBase64' => 'data:image/png;base64,' . $pixData['encodedImage'],
                'expiresAt' => $pixData['expirationDate'] ?? now()->addHours(24)->toIso8601String(),
                'devMode' => config('services.asaas.sandbox', false),
                'gateway' => 'asaas',
            ];

        } catch (\Exception $e) {
            Log::error('Exceção ao criar PIX no Asaas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Consulta status de uma cobrança
     */
    public function getPayment(string $paymentId): array
    {
        try {
            $response = $this->asaasClient()
                ->get("{$this->baseUrl}/payments/{$paymentId}");

            if (!$response->successful()) {
                throw new \Exception('Erro ao consultar cobrança no Asaas.');
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Erro ao consultar cobrança no Asaas', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Valida webhook do Asaas
     */
    public function validateWebhook(array $payload): bool
    {
        return isset($payload['event']) && isset($payload['payment']);
    }

    /**
     * Processa webhook do Asaas
     */
    public function processWebhook(array $payload): array
    {
        $event = $payload['event'] ?? null;
        $payment = $payload['payment'] ?? null;

        if (!$payment) {
            throw new \Exception('Dados do pagamento não encontrados no webhook');
        }

        // Mapear status do Asaas
        $statusMap = [
            'CONFIRMED' => 'PAID',
            'RECEIVED' => 'PAID',
            'PENDING' => 'PENDING',
            'OVERDUE' => 'EXPIRED',
            'REFUNDED' => 'REFUNDED',
            'RECEIVED_IN_CASH' => 'PAID',
        ];

        return [
            'event' => $event,
            'billing_id' => $payment['id'] ?? null,
            'status' => $statusMap[$payment['status'] ?? ''] ?? $payment['status'],
            'amount' => (float) ($payment['value'] ?? 0),
            'external_id' => $payment['externalReference'] ?? null,
            'metadata' => [],
            'paid' => in_array($payment['status'] ?? '', ['CONFIRMED', 'RECEIVED', 'RECEIVED_IN_CASH']),
            'gateway' => 'asaas',
        ];
    }
}
