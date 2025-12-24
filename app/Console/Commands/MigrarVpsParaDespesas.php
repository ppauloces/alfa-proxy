<?php

namespace App\Console\Commands;

use App\Models\Vps;
use App\Models\Despesa;
use Illuminate\Console\Command;

class MigrarVpsParaDespesas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vps:migrar-despesas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra VPS que não estão registradas na tabela de despesas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migração de VPS para despesas...');

        // Buscar todas as VPS
        $vps = Vps::all();

        if ($vps->isEmpty()) {
            $this->warn('Nenhuma VPS encontrada no sistema.');
            return 0;
        }

        $this->info("Encontradas {$vps->count()} VPS no sistema.");

        $criadas = 0;
        $jaExistentes = 0;

        foreach ($vps as $vpsItem) {
            // Verificar se já existe uma despesa de compra para esta VPS
            $despesaExistente = Despesa::where('vps_id', $vpsItem->id)
                ->where('tipo', 'compra')
                ->first();

            if ($despesaExistente) {
                $this->line("VPS #{$vpsItem->id} ({$vpsItem->apelido}) - Já possui despesa de compra");
                $jaExistentes++;
                continue;
            }

            // Criar despesa de compra inicial
            Despesa::create([
                'vps_id' => $vpsItem->id,
                'tipo' => 'compra',
                'valor' => $vpsItem->valor,
                'descricao' => 'VPS ' . $vpsItem->apelido . ' - Compra inicial (migração automática)',
                'data_vencimento' => $vpsItem->data_contratacao,
                'data_pagamento' => $vpsItem->data_contratacao,
                'status' => 'pago',
            ]);

            $this->info("✓ VPS #{$vpsItem->id} ({$vpsItem->apelido}) - Despesa de compra criada: R$ " . number_format($vpsItem->valor, 2, ',', '.'));
            $criadas++;
        }

        $this->newLine();
        $this->info('========================================');
        $this->info('Migração concluída!');
        $this->info("Despesas criadas: {$criadas}");
        $this->info("Já existentes: {$jaExistentes}");
        $this->info('========================================');

        return 0;
    }
}
