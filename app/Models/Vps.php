<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vps extends Model
{
    protected $table = 'vps';

    protected $fillable = [
        'apelido',
        'ip',
        'usuario_ssh',
        'senha_ssh',
        'valor',
        'valor_renovacao',
        'pais',
        'hospedagem',
        'periodo_dias',
        'data_contratacao',
        'status',
        'status_geracao',
        'erro_geracao',
        'proxies_geradas',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'valor_renovacao' => 'decimal:2',
        'data_contratacao' => 'date',
    ];

    // Relacionamento com proxies (stocks)
    public function proxies()
    {
        return $this->hasMany(Stock::class, 'vps_id');
    }

    public function despesas()
    {
        return $this->hasMany(Despesa::class);
    }

    // Helper para pegar o custo total acumulado dessa VPS
    public function custoTotal()
    {
        return $this->despesas()->sum('valor');
    }

    /**
     * Calcula a próxima data de renovação baseado na data de contratação e período
     */
    public function proximaRenovacao(): ?\Carbon\Carbon
    {
        if (!$this->data_contratacao || !$this->periodo_dias) {
            return null;
        }

        $dataBase = \Carbon\Carbon::parse($this->data_contratacao);
        $hoje = \Carbon\Carbon::today();

        // Encontra a próxima data de renovação
        while ($dataBase->lte($hoje)) {
            $dataBase->addDays($this->periodo_dias);
        }

        return $dataBase;
    }

    /**
     * Retorna o valor de renovação (usa valor_renovacao se definido, senão usa valor)
     */
    public function getValorRenovacaoEfetivoAttribute(): float
    {
        return $this->valor_renovacao ?? $this->valor ?? 0;
    }
}