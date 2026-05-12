<?php

namespace App\Console\Commands;

use App\Jobs\RecycleExpiredProxy;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Bus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus as BusFacade;
use Illuminate\Support\Facades\Log;

class RecycleExpiredProxies extends Command
{
    protected $signature = 'proxies:recycle-expired
                            {--carencia-dias=7 : Dias apos a expiracao para considerar elegivel a reciclagem}
                            {--batch-size=50 : Tamanho do batch de jobs}';

    protected $description = 'Recicla proxies bloqueadas ha mais de N dias e devolve ao estoque com novas credenciais';

    public function handle(): int
    {
        $carenciaDias = (int) $this->option('carencia-dias');
        $batchSize = (int) $this->option('batch-size');

        $limiar = Carbon::now()->subDays($carenciaDias);

        $stocks = Stock::whereNotNull('user_id')
            ->where('bloqueada', true)
            ->whereNotNull('expiracao')
            ->where('expiracao', '<=', $limiar)
            ->whereNull('recycled_at')
            ->select('id')
            ->get();

        if ($stocks->isEmpty()) {
            $this->info('Nenhuma proxy elegivel para reciclagem automatica.');
            return Command::SUCCESS;
        }

        $this->info("Encontradas {$stocks->count()} proxies para reciclar.");

        $batchJobs = [];
        foreach ($stocks as $stock) {
            $batchJobs[] = new RecycleExpiredProxy($stock->id);

            if (count($batchJobs) >= $batchSize) {
                $this->dispatchBatch($batchJobs);
                $batchJobs = [];
            }
        }

        if (!empty($batchJobs)) {
            $this->dispatchBatch($batchJobs);
        }

        $this->info("{$stocks->count()} jobs de reciclagem enfileirados.");
        Log::info('Reciclagem automatica de proxies enfileirada', [
            'total' => $stocks->count(),
            'carencia_dias' => $carenciaDias,
        ]);

        return Command::SUCCESS;
    }

    private function dispatchBatch(array $jobs): void
    {
        BusFacade::batch($jobs)
            ->name('recycle-expired-proxies-' . now()->format('Y-m-d-H-i-s'))
            ->allowFailures()
            ->onQueue('recycling')
            ->dispatch();
    }
}
