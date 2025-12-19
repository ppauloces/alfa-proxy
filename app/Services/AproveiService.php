<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AproveiService
{
    protected $baseUrl;
    protected $secretKey;

    public function __construct()
    {
        $this->baseUrl = 'https://api.aproveipay.com.br/v1';
        $this->secretKey = config('services.aprovei.secret_key');
    }

    /**
     * Criar uma transação com cartão de crédito
     */
    public function createCreditCardTransaction(array $data)
    {
        try {
            if (empty($this->secretKey)) {
                Log::error('APROVEI_SECRET_KEY não configurada (services.aprovei.secret_key vazio).');

                return [
                    'success' => false,
                    'error' => 'Chave da Aprovei não configurada (APROVEI_SECRET_KEY).',
                ];
            }

            // A API da Aprovei usa Basic Auth (secret_key como username e senha vazia),
            // conforme exemplo de integração legado do próprio projeto.
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->asJson()
                ->timeout(30)
                ->post("{$this->baseUrl}/transactions", $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Erro ao criar transação Aprovei', [
                'status' => $response->status(),
                'response' => $response->json(),
                'response_raw' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Erro ao processar pagamento',
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Exceção ao criar transação Aprovei', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao conectar com processador de pagamentos: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Montar payload de transação com cartão de crédito
     */
    public function buildCreditCardPayload(
        int $amountInCents,
        string $cardToken,
        int $installments,
        array $customer,
        array $items,
        string $externalRef,
        ?string $postbackUrl = null,
        ?string $ip = null
    ) {
        $payload = [
            'amount' => $amountInCents,
            'currency' => 'BRL',
            'paymentMethod' => 'credit_card',
            'installments' => $installments,
            'card' => [
                // A API espera "hash" (o SDK JS retorna um hash/token do cartão).
                'hash' => $cardToken,
            ],
            'customer' => $customer,
            'items' => $items,
            'externalRef' => $externalRef,
        ];

        if ($postbackUrl) {
            $payload['postbackUrl'] = $postbackUrl;
        }

        if ($ip) {
            $payload['ip'] = $ip;
        }

        return $payload;
    }

    /**
     * Formatar dados do cliente para Aprovei
     */
    public function formatCustomer(\App\Models\User $user)
    {
        $customer = [
            'externalRef' => 'user-' . $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        // Adicionar CPF apenas se existir
        if (!empty($user->cpf)) {
            $customer['document'] = [
                'number' => preg_replace('/\D/', '', $user->cpf),
                'type' => 'cpf',
            ];
        }

        return $customer;
    }

    /**
     * Formatar item de proxy para Aprovei
     */
    public function formatProxyItem(int $quantity, int $priceInCents, int $periodDays, string $country)
    {
        return [
            'externalRef' => 'proxy-' . time(),
            'title' => "Proxy SOCKS5 {$country} - {$periodDays} dias",
            'unitPrice' => $priceInCents,
            'quantity' => $quantity,
            'tangible' => false,
        ];
    }

    /**
     * Formatar item de recarga de saldo para Aprovei
     */
    public function formatBalanceItem(int $amountInCents)
    {
        $amountFormatted = number_format($amountInCents / 100, 2, ',', '.');

        return [
            'externalRef' => 'balance-' . time(),
            'title' => "Recarga de Saldo - R$ {$amountFormatted}",
            'unitPrice' => $amountInCents,
            'quantity' => 1,
            'tangible' => false,
        ];
    }
}
