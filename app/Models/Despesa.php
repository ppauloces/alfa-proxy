<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Despesa extends Model
{
    protected $fillable = [
        'vps_id',
        'tipo',
        'valor',
        'descricao',
        'data_vencimento',
        'data_pagamento',
        'status',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
    ];

    public function vps()
    {
        return $this->belongsTo(Vps::class);
    }
}