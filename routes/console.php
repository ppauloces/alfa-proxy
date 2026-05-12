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
 * Recuperação de VPS travadas em pending/processing
 * Roda a cada hora — reencaminha para a fila 'proxies' qualquer VPS parada há mais de 45 min
 */
Schedule::command('vps:recover-stuck')
    ->hourly()
    ->name('recover-stuck-vps')
    ->withoutOverlapping(5)
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Verificação de VPS travadas executada com sucesso');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Falha na verificação de VPS travadas');
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

/**
 * Aviso 24h antes da reciclagem automatica
 * Proxies bloqueadas ha mais de (carencia - 24h) recebem e-mail de aviso
 */
Schedule::command('proxies:notify-recycling --carencia-dias=7 --aviso-horas=24')
    ->hourly()
    ->name('notify-proxy-recycling')
    ->withoutOverlapping(10)
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Aviso de reciclagem enviado com sucesso');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Falha no envio de avisos de reciclagem');
    });

/**
 * Reciclagem automatica de proxies bloqueadas ha 7+ dias
 * Regenera senha no servidor, desbloqueia porta e devolve ao estoque
 */
Schedule::command('proxies:recycle-expired --carencia-dias=7 --batch-size=50')
    ->hourly()
    ->name('recycle-expired-proxies')
    ->withoutOverlapping(15)
    ->runInBackground()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Reciclagem automatica de proxies executada com sucesso');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Falha na reciclagem automatica de proxies');
    });
