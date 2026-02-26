<?php

namespace App\Console\Commands;

use App\Jobs\GerarProxiesJob;
use App\Models\Vps;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class RecoverStuckVps extends Command
{
    /**
     * Considera uma VPS travada se ficou em pending/processing por mais que este tempo (minutos)
     */
    const STUCK_THRESHOLD_MINUTES = 45;

    protected $signature = 'vps:recover-stuck
                            {--dry-run : Apenas lista as VPS travadas sem reencaminhar}
                            {--minutes= : Minutos sem atualização para considerar travada (padrão: 45)}';

    protected $description = 'Detecta VPS presas em pending/processing e as recoloca na fila de geração';

    public function handle(): int
    {
        $minutes = (int) ($this->option('minutes') ?: self::STUCK_THRESHOLD_MINUTES);
        $dryRun  = $this->option('dry-run');
        $cutoff  = Carbon::now()->subMinutes($minutes);

        $stuck = Vps::whereIn('status_geracao', ['pending', 'processing'])
            ->where('updated_at', '<=', $cutoff)
            ->get();

        if ($stuck->isEmpty()) {
            $this->info("Nenhuma VPS travada encontrada (threshold: {$minutes} min).");
            return Command::SUCCESS;
        }

        $this->warn("Encontradas {$stuck->count()} VPS travadas (sem atualização há mais de {$minutes} min):");

        foreach ($stuck as $vps) {
            $idleMinutes = (int) Carbon::parse($vps->updated_at)->diffInMinutes(now());
            $this->line("  - [{$vps->id}] {$vps->apelido} ({$vps->ip}) — status: {$vps->status_geracao} — parada há {$idleMinutes} min");
        }

        if ($dryRun) {
            $this->comment('Modo dry-run: nenhuma ação foi tomada.');
            return Command::SUCCESS;
        }

        $requeued = 0;

        foreach ($stuck as $vps) {
            $vps->update([
                'status_geracao' => 'pending',
                'erro_geracao'   => null,
            ]);

            GerarProxiesJob::dispatch($vps, $vps->periodo_dias ?? 30, 0)->onQueue('proxies');

            Log::warning('VPS travada detectada e reencaminhada para a fila', [
                'vps_id'   => $vps->id,
                'vps_ip'   => $vps->ip,
                'idle_min' => (int) Carbon::parse($vps->updated_at)->diffInMinutes(now()),
            ]);

            $requeued++;
        }

        $this->info("{$requeued} VPS reencaminhadas para a fila 'proxies'.");

        Log::info('Recuperação de VPS travadas concluída', [
            'total_encontradas'   => $stuck->count(),
            'total_reencaminhadas' => $requeued,
            'threshold_minutes'   => $minutes,
        ]);

        return Command::SUCCESS;
    }
}
