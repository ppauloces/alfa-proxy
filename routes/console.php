<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Agendamento automático de verificação de proxies expirados
 *
 * Executa a cada 5 minutos para garantir bloqueio rápido após expiração
 * Usa batching otimizado para processar grandes volumes
 */
Schedule::command('proxies:check-expired --batch-size=100')
    ->everyFiveMinutes()
    ->name('check-expired-proxies')
    ->withoutOverlapping(10) // Previne execuções sobrepostas (timeout 10min)
    ->runInBackground() // Executa em background para não bloquear
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Verificação de proxies expirados executada com sucesso via scheduler');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Falha na execução do scheduler de proxies expirados');
    });

/**
 * Agendamento automático de geração de despesas de renovação de VPS
 *
 * Executa diariamente às 00:01 para registrar despesas de renovação
 * que são cobradas no dia (cobrança automática no cartão)
 */
Schedule::command('vps:gerar-despesas-renovacao')
    ->dailyAt('00:01')
    ->name('gerar-despesas-renovacao-vps')
    ->withoutOverlapping(5)
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Geração de despesas de renovação de VPS executada com sucesso');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Falha na geração de despesas de renovação de VPS');
    });

/**
 * Auto-renovacao de proxies via cartao (Stripe)
 */
Schedule::command('proxies:auto-renew --hours-before=12')
    ->hourly()
    ->name('auto-renew-proxies')
    ->withoutOverlapping(10)
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Auto-renovacao de proxies executada com sucesso');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Falha na auto-renovacao de proxies');
    });
