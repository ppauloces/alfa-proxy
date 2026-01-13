<?php

namespace App\Console\Commands;

use App\Jobs\BlockExpiredProxy;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class CheckExpiredProxies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proxies:check-expired
                            {--batch-size=100 : Número de proxies por batch}
                            {--force : Força bloqueio mesmo que já esteja bloqueado}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica e bloqueia proxies expirados automaticamente via fila otimizada';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando proxies expirados...');

        $batchSize = (int) $this->option('batch-size');
        $force = $this->option('force');

        // Query otimizada: busca proxies expirados que não estão bloqueados
        $query = Stock::whereNotNull('expiracao')
            ->where('expiracao', '<=', Carbon::now());

        if (!$force) {
            $query->where('bloqueada', false);
        }

        // Conta total antes de processar
        $totalExpired = $query->count();

        if ($totalExpired === 0) {
            $this->info('✅ Nenhum proxy expirado encontrado.');
            return Command::SUCCESS;
        }

        $this->warn("📊 Encontrados {$totalExpired} proxies expirados para bloquear");

        // Processar em chunks para não sobrecarregar memória
        $processedCount = 0;
        $batchJobs = [];

        $progressBar = $this->output->createProgressBar($totalExpired);
        $progressBar->start();

        // Busca IDs em chunks e cria jobs
        $query->select('id')
            ->chunk(1000, function ($stocks) use (&$batchJobs, &$processedCount, $batchSize, $progressBar) {
                foreach ($stocks as $stock) {
                    $batchJobs[] = new BlockExpiredProxy($stock->id);
                    $processedCount++;
                    $progressBar->advance();

                    // Despachar em batches quando atingir o tamanho definido
                    if (count($batchJobs) >= $batchSize) {
                        $this->dispatchBatch($batchJobs);
                        $batchJobs = [];
                    }
                }
            });

        // Despachar jobs restantes
        if (count($batchJobs) > 0) {
            $this->dispatchBatch($batchJobs);
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ {$processedCount} jobs de bloqueio enfileirados com sucesso!");
        $this->comment('💡 Os proxies serão bloqueados em background pela fila "expiration"');
        $this->comment('💡 Execute: php artisan queue:work --queue=expiration');

        Log::info('Verificação de proxies expirados concluída', [
            'total_encontrados' => $totalExpired,
            'jobs_enfileirados' => $processedCount,
            'batch_size' => $batchSize,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Despacha um batch de jobs para a fila
     */
    private function dispatchBatch(array $jobs): void
    {
        // Usa Bus::batch para melhor controle e performance
        Bus::batch($jobs)
            ->name('block-expired-proxies-' . now()->format('Y-m-d-H-i-s'))
            ->allowFailures() // Permite que alguns jobs falhem sem parar o batch
            ->onQueue('expiration')
            ->dispatch();
    }
}
