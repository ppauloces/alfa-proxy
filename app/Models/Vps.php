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
}