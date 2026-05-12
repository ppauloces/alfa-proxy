<?php

namespace App\Console\Commands;

use App\Mail\ProxyRecyclingWarningMail;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyProxyRecycling extends Command
{
    protected $signature = 'proxies:notify-recycling
                            {--carencia-dias=7 : Periodo de carencia apos expiracao antes da reciclagem}
                            {--aviso-horas=24 : Quantas horas antes da reciclagem o aviso deve ser enviado}';

    protected $description = 'Envia e-mail de aviso aos usuarios cujas proxies bloqueadas serao recicladas em breve';

    public function handle(): int
    {
        $carenciaDias = (int) $this->option('carencia-dias');
        $avisoHoras = (int) $this->option('aviso-horas');

        $limiarReciclagem = Carbon::now()->subDays($carenciaDias);
        $limiarAviso = Carbon::now()->subDays($carenciaDias)->addHours($avisoHoras);

        $stocks = Stock::with('user', 'vps')
            ->whereNotNull('user_id')
            ->where('bloqueada', true)
            ->whereNull('recycling_notified_at')
            ->whereNull('recycled_at')
            ->whereNotNull('expiracao')
            ->where('expiracao', '<=', $limiarAviso)
            ->where('expiracao', '>', $limiarReciclagem)
            ->get();

        if ($stocks->isEmpty()) {
            $this->info('Nenhuma proxy elegivel para aviso de reciclagem.');
            return Command::SUCCESS;
        }

        $this->info("Encontrados {$stocks->count()} proxies para notificar.");

        $enviados = 0;
        foreach ($stocks as $stock) {
            if (!$stock->user || !$stock->user->email) {
                $stock->update(['recycling_notified_at' => now()]);
                continue;
            }

            $recicladaEm = Carbon::parse($stock->expiracao)->addDays($carenciaDias);

            try {
                Mail::to($stock->user->email)->send(
                    new ProxyRecyclingWarningMail(
                        $stock->user->name ?? $stock->user->username ?? 'Cliente',
                        $stock,
                        $recicladaEm,
                    )
                );

                $stock->update(['recycling_notified_at' => now()]);
                $enviados++;
            } catch (\Throwable $e) {
                Log::error('Falha ao enviar aviso de reciclagem', [
                    'stock_id' => $stock->id,
                    'user_id' => $stock->user_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("{$enviados} avisos enviados com sucesso.");
        Log::info('Avisos de reciclagem processados', [
            'total' => $stocks->count(),
            'enviados' => $enviados,
        ]);

        return Command::SUCCESS;
    }
}
