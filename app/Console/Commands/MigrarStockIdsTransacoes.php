<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\Transaction;
use Illuminate\Console\Command;

class MigrarStockIdsTransacoes extends Command
{
    protected $signature = 'transacoes:migrar-stock-ids
                            {--dry-run : Apenas simula, sem salvar no banco}
                            {--window=120 : Janela de tempo em segundos para o match (padrão: 120)}';

    protected $description = 'Preenche stock_ids nas transações de compra_proxy (proximidade temporal) e renovacao (metadata.proxy_id direto)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $window = (int) $this->option('window');

        $this->info($dryRun ? '[DRY-RUN] Simulação — nada será salvo.' : 'Iniciando migração...');
        $this->newLine();

        $totalLinked   = 0;
        $totalSkipped  = 0;
        $totalConflict = 0;

        // ─────────────────────────────────────────────
        // 1) RENOVAÇÕES — proxy_id já está em metadata
        // ─────────────────────────────────────────────
        $renovacoes = Transaction::where('tipo', 'renovacao')
            ->whereNull('stock_ids')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->info("Renovações sem stock_ids: {$renovacoes->count()}");

        foreach ($renovacoes as $txn) {
            $proxyId = $txn->metadata['proxy_id'] ?? null;

            if (!$proxyId) {
                $this->line("  <fg=yellow>SKIP</> Txn #{$txn->id} ({$txn->transacao}) — metadata.proxy_id ausente");
                $totalSkipped++;
                continue;
            }

            $this->line("  <fg=green>LINK</> Txn #{$txn->id} (renovacao) → stock_ids: [{$proxyId}]");
            $totalLinked++;

            if (!$dryRun) {
                $txn->stock_ids = [$proxyId];
                $txn->save();
            }
        }

        $this->newLine();

        // ─────────────────────────────────────────────
        // 2) COMPRAS — match por proximidade temporal
        // ─────────────────────────────────────────────
        $compras = Transaction::where('tipo', 'compra_proxy')
            ->where('status', 1)
            ->whereNull('stock_ids')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->info("Compras sem stock_ids: {$compras->count()}");

        foreach ($compras as $txn) {
            $txnTs = strtotime($txn->updated_at);

            $candidatos = Stock::where('user_id', $txn->user_id)
                ->where('disponibilidade', false)
                ->get()
                ->filter(function ($stock) use ($txnTs, $window) {
                    return abs(strtotime($stock->updated_at) - $txnTs) < $window;
                });

            if ($candidatos->isEmpty()) {
                $this->line("  <fg=yellow>SKIP</> Txn #{$txn->id} ({$txn->transacao}) — nenhum stock próximo encontrado");
                $totalSkipped++;
                continue;
            }

            $ids = $candidatos->pluck('id')->sort()->values()->toArray();
            $quantidade = $txn->metadata['quantidade'] ?? null;

            if ($quantidade !== null && count($ids) !== (int) $quantidade) {
                $this->line(
                    "  <fg=cyan>WARN</> Txn #{$txn->id} — esperado {$quantidade} stock(s), encontrado " . count($ids) . ": [" . implode(',', $ids) . "]"
                );
                $totalConflict++;
            } else {
                $this->line("  <fg=green>LINK</> Txn #{$txn->id} (compra) → stock_ids: [" . implode(',', $ids) . "]");
                $totalLinked++;
            }

            if (!$dryRun) {
                $txn->stock_ids = $ids;
                $txn->save();
            }
        }

        $this->newLine();
        $this->table(
            ['Resultado', 'Quantidade'],
            [
                ['Vinculadas (ok)',          $totalLinked],
                ['Vinculadas (divergência)', $totalConflict],
                ['Sem match (skip)',         $totalSkipped],
                ['Total processadas',        $renovacoes->count() + $compras->count()],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->warn('Dry-run concluído. Rode sem --dry-run para persistir.');
        } else {
            $this->info('Migração concluída!');
        }

        return self::SUCCESS;
    }
}
