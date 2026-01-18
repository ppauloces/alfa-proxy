<?php

namespace App\Console\Commands;

use App\Models\Despesa;
use App\Models\Vps;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GerarDespesasRenovacao extends Command
{
    protected $signature = 'vps:gerar-despesas-renovacao
                            {--retroativo : Também gera despesas retroativas não lançadas}
                            {--force : Força a geração mesmo se já existir despesa para o período}';

    protected $description = 'Gera despesas de renovação para VPS (hoje e retroativas se especificado)';

    public function handle()
    {
        $force = $this->option('force');
        $retroativo = $this->option('retroativo');

        $this->info("Verificando VPS com renovação...");

        // Apenas VPS com status "Operacional" geram despesas de renovação
        $vpsAtivas = Vps::whereNotNull('data_contratacao')
            ->whereNotNull('periodo_dias')
            ->where('status', 'Operacional')
            ->get();

        $despesasCriadas = 0;
        $despesasExistentes = 0;
        $hoje = Carbon::today();

        foreach ($vpsAtivas as $vps) {
            // Usa valor_renovacao se definido, senão usa valor
            $valorRenovacao = $vps->valor_renovacao ?? $vps->valor;

            if ($valorRenovacao <= 0) {
                continue;
            }

            // Calcular todas as datas de renovação desde a contratação
            $datasRenovacao = $this->calcularDatasRenovacao($vps, $hoje, $retroativo);

            foreach ($datasRenovacao as $dataRenovacao) {
                // Verificar se já existe despesa de renovação para esta data
                $despesaExistente = Despesa::where('vps_id', $vps->id)
                    ->where('tipo', 'renovacao')
                    ->whereDate('data_vencimento', $dataRenovacao->toDateString())
                    ->exists();

                if ($despesaExistente && !$force) {
                    $this->line("VPS #{$vps->id} ({$vps->apelido}) - Despesa já existe para {$dataRenovacao->format('d/m/Y')}");
                    $despesasExistentes++;
                    continue;
                }

                // Criar a despesa de renovação (já vem paga pois é cobrada automaticamente no cartão)
                Despesa::create([
                    'vps_id' => $vps->id,
                    'tipo' => 'renovacao',
                    'valor' => $valorRenovacao,
                    'descricao' => "Renovação VPS {$vps->apelido} - Período {$vps->periodo_dias} dias",
                    'data_vencimento' => $dataRenovacao,
                    'data_pagamento' => $dataRenovacao,
                    'status' => 'pago',
                ]);

                $isRetroativa = $dataRenovacao->lt($hoje);
                $label = $isRetroativa ? '[RETROATIVA]' : '';
                $this->info("VPS #{$vps->id} ({$vps->apelido}) {$label} - Despesa criada para {$dataRenovacao->format('d/m/Y')} - R$ " . number_format($valorRenovacao, 2, ',', '.'));
                $despesasCriadas++;
            }
        }

        $this->newLine();
        $this->info("Resumo:");
        $this->info("- Despesas criadas: {$despesasCriadas}");
        $this->info("- Despesas já existentes: {$despesasExistentes}");
        $this->info("- Total de VPS verificadas: {$vpsAtivas->count()}");

        return Command::SUCCESS;
    }

    /**
     * Calcula todas as datas de renovação para uma VPS
     *
     * @param Vps $vps
     * @param Carbon $hoje
     * @param bool $incluirRetroativas
     * @return array<Carbon>
     */
    private function calcularDatasRenovacao(Vps $vps, Carbon $hoje, bool $incluirRetroativas): array
    {
        $datasRenovacao = [];
        $dataContratacao = Carbon::parse($vps->data_contratacao);
        $periodoDias = $vps->periodo_dias;

        // Primeira renovação é após o período inicial
        $dataRenovacao = $dataContratacao->copy()->addDays($periodoDias);

        // Se retroativo, incluir todas as renovações desde a contratação até hoje
        if ($incluirRetroativas) {
            while ($dataRenovacao->lte($hoje)) {
                $datasRenovacao[] = $dataRenovacao->copy();
                $dataRenovacao->addDays($periodoDias);
            }
        } else {
            // Apenas renovação de hoje
            while ($dataRenovacao->lt($hoje)) {
                $dataRenovacao->addDays($periodoDias);
            }

            if ($dataRenovacao->isSameDay($hoje)) {
                $datasRenovacao[] = $dataRenovacao->copy();
            }
        }

        return $datasRenovacao;
    }
}
