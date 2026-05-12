<?php

namespace App\Console\Commands;

use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecycleProxiesManual extends Command
{
    protected $signature = 'proxies:recycle-manual
                            {--carencia-dias=7 : Dias apos a expiracao para considerar elegivel}
                            {--stock-id= : Recicla apenas o stock com este ID (ignora carencia)}
                            {--limit= : Limita a quantidade de proxies processadas}
                            {--dry-run : Apenas lista as proxies elegiveis, sem reciclar}
                            {--force : Nao pede confirmacao antes de processar}';

    protected $description = 'Reciclagem MANUAL: chama a API Python sincronicamente e reseta proxies bloqueadas nao renovadas';

    public function handle(): int
    {
        $carenciaDias = (int) $this->option('carencia-dias');
        $stockIdOpt = $this->option('stock-id');
        $limit = $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $query = Stock::with('vps', 'user');

        if ($stockIdOpt) {
            $query->where('id', (int) $stockIdOpt);
        } else {
            $limiar = Carbon::now()->subDays($carenciaDias);
            $query->whereNotNull('user_id')
                ->where('bloqueada', true)
                ->whereNotNull('expiracao')
                ->where('expiracao', '<=', $limiar)
                ->whereNull('recycled_at');
        }

        if ($limit) {
            $query->limit((int) $limit);
        }

        $stocks = $query->get();

        if ($stocks->isEmpty()) {
            $this->info('Nenhuma proxy elegivel.');
            return Command::SUCCESS;
        }

        $this->table(
            ['ID', 'IP:Porta', 'Usuario', 'Cliente', 'Expirou em', 'Bloqueada'],
            $stocks->map(fn ($s) => [
                $s->id,
                "{$s->ip}:{$s->porta}",
                $s->usuario,
                $s->user?->email ?? '-',
                $s->expiracao ? Carbon::parse($s->expiracao)->format('d/m/Y H:i') : '-',
                $s->bloqueada ? 'sim' : 'nao',
            ])->toArray()
        );

        $this->info("Total elegivel: {$stocks->count()}");

        if ($dryRun) {
            $this->comment('[dry-run] nenhuma alteracao realizada.');
            return Command::SUCCESS;
        }

        if (!$force && !$this->confirm("Reciclar {$stocks->count()} proxy(ies)?", false)) {
            $this->warn('Cancelado.');
            return Command::SUCCESS;
        }

        $pythonApiUrl = config('services.python_api.url', 'http://127.0.0.1:8001');
        $ok = 0;
        $fail = 0;

        $bar = $this->output->createProgressBar($stocks->count());
        $bar->start();

        foreach ($stocks as $stock) {
            $bar->advance();

            if (!$stock->vps) {
                $this->newLine();
                $this->error("Stock {$stock->id}: VPS nao encontrada, pulando.");
                $fail++;
                continue;
            }

            try {
                $response = Http::timeout(60)->post("{$pythonApiUrl}/reciclar", [
                    'ip_vps'        => $stock->vps->ip,
                    'user_ssh'      => $stock->vps->usuario_ssh,
                    'senha_ssh'     => $stock->vps->senha_ssh,
                    'usuario_proxy' => $stock->usuario,
                    'porta'         => $stock->porta,
                ]);

                if (!$response->successful()) {
                    $this->newLine();
                    $this->error("Stock {$stock->id}: API retornou {$response->status()} - " . substr($response->body(), 0, 200));
                    Log::error('Reciclagem manual falhou', [
                        'stock_id' => $stock->id,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    $fail++;
                    continue;
                }

                $novaSenha = $response->json('nova_senha') ?? $stock->senha;

                DB::transaction(function () use ($stock, $novaSenha) {
                    $stock->update([
                        'senha'                 => $novaSenha,
                        'user_id'               => null,
                        'disponibilidade'       => true,
                        'bloqueada'             => false,
                        'substituido'           => false,
                        'substituido_por'       => null,
                        'expiracao'             => null,
                        'periodo_dias'          => null,
                        'motivo_uso'            => null,
                        'renovacao_automatica'  => false,
                        'recycled_at'           => now(),
                        'recycling_notified_at' => null,
                    ]);
                });

                $ok++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("Stock {$stock->id}: excecao - {$e->getMessage()}");
                Log::error('Reciclagem manual: excecao', [
                    'stock_id' => $stock->id,
                    'error' => $e->getMessage(),
                ]);
                $fail++;
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Concluido: {$ok} reciclada(s), {$fail} falha(s).");

        return $fail > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
