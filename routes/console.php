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
