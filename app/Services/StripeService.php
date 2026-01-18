<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
    }

    /**
     * Criar uma cobrança usando PaymentMethod salvo
     */
    public function charge(array $data): array
    {
        try {
            if (empty(config('services.stripe.secret_key'))) {
                Log::error('STRIPE_SECRET_KEY não configurada.');

                return [
                    'success' => false,
                    'error' => 'Chave da Stripe não configurada.',
                ];
            }

            // Criar ou recuperar Customer no Stripe
            $customerId = $this->getOrCreateCustomer($data['customer']);

            // Anexar PaymentMethod ao Customer se necessário
            $this->attachPaymentMethodToCustomer($data['payment_method_id'], $customerId);

            // Criar PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => $data['amount'], // em centavos
                'currency' => 'brl',
                'customer' => $customerId,
                'payment_method' => $data['payment_method_id'],
                'off_session' => true,
                'confirm' => true,
                'description' => $data['description'] ?? 'Compra de Proxies',
                'metadata' => $data['metadata'] ?? [],
            ]);

            Log::info('PaymentIntent criado com sucesso', [
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
            ]);

            return [
                'success' => true,
                'data' => [
                    'id' => $paymentIntent->id,
                    'status' => $this->mapStatus($paymentIntent->status),
                    'amount' => $paymentIntent->amount,
                    'currency' => $paymentIntent->currency,
                ],
            ];

        } catch (ApiErrorException $e) {
            Log::error('Erro na API Stripe', [
                'error' => $e->getMessage(),
                'code' => $e->getStripeCode(),
            ]);

            $errorMessage = $this->translateStripeError($e);

            return [
                'success' => false,
                'error' => $errorMessage,
                'requires_action' => $e->getStripeCode() === 'authentication_required',
            ];
        } catch (\Exception $e) {
            Log::error('Exceção ao processar pagamento Stripe', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Criar ou recuperar Customer no Stripe
     */
    protected function getOrCreateCustomer(array $customerData): string
    {
        // Buscar por email
        $customers = Customer::all([
            'email' => $customerData['email'],
            'limit' => 1,
        ]);

        if (count($customers->data) > 0) {
            return $customers->data[0]->id;
        }

        // Criar novo customer
        $customer = Customer::create([
            'email' => $customerData['email'],
            'name' => $customerData['name'],
            'metadata' => [
                'user_id' => $customerData['user_id'] ?? null,
            ],
        ]);

        return $customer->id;
    }

    /**
     * Anexar PaymentMethod ao Customer
     */
    protected function attachPaymentMethodToCustomer(string $paymentMethodId, string $customerId): void
    {
        try {
            $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);

            // Se já está anexado ao customer, não fazer nada
            if ($paymentMethod->customer === $customerId) {
                return;
            }

            // Se está anexado a outro customer, desanexar primeiro
            if ($paymentMethod->customer) {
                $paymentMethod->detach();
            }

            // Anexar ao customer
            $paymentMethod->attach(['customer' => $customerId]);

        } catch (ApiErrorException $e) {
            // Se já está anexado, ignorar o erro
            if (strpos($e->getMessage(), 'already been attached') === false) {
                throw $e;
            }
        }
    }

    /**
     * Mapear status do Stripe para status interno
     */
    protected function mapStatus(string $stripeStatus): string
    {
        return match ($stripeStatus) {
            'succeeded' => 'paid',
            'processing' => 'pending',
            'requires_payment_method' => 'failed',
            'requires_confirmation' => 'pending',
            'requires_action' => 'requires_action',
            'canceled' => 'canceled',
            default => 'pending',
        };
    }

    /**
     * Traduzir erros do Stripe para português
     */
    protected function translateStripeError(ApiErrorException $e): string
    {
        $code = $e->getStripeCode();

        return match ($code) {
            'card_declined' => 'Cartão recusado. Verifique os dados ou tente outro cartão.',
            'expired_card' => 'Cartão expirado. Por favor, use outro cartão.',
            'incorrect_cvc' => 'Código de segurança incorreto.',
            'processing_error' => 'Erro ao processar o cartão. Tente novamente.',
            'incorrect_number' => 'Número do cartão inválido.',
            'authentication_required' => 'Autenticação necessária. Por favor, aprove a transação no app do seu banco.',
            'insufficient_funds' => 'Saldo insuficiente no cartão.',
            default => $e->getMessage(),
        };
    }

    /**
     * Formatar dados do cliente para Stripe
     */
    public function formatCustomer(\App\Models\User $user): array
    {
        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    /**
     * Try charging with a list of saved PaymentMethods (fallback).
     */
    public function chargeWithFallback(array $data, array $paymentMethodIds): array
    {
        $attempts = [];

        foreach ($paymentMethodIds as $paymentMethodId) {
            $attemptData = $data;
            $attemptData['payment_method_id'] = $paymentMethodId;

            $result = $this->charge($attemptData);
            $attempts[] = [
                'payment_method_id' => $paymentMethodId,
                'success' => $result['success'] ?? false,
                'error' => $result['error'] ?? null,
                'requires_action' => $result['requires_action'] ?? false,
            ];

            if (!empty($result['success'])) {
                return [
                    'success' => true,
                    'data' => $result['data'] ?? [],
                    'payment_method_id' => $paymentMethodId,
                    'attempts' => $attempts,
                ];
            }
        }

        $lastError = $attempts
            ? ($attempts[array_key_last($attempts)]['error'] ?? 'Charge failed')
            : 'No payment methods provided';

        return [
            'success' => false,
            'error' => $lastError,
            'attempts' => $attempts,
        ];
    }
}
