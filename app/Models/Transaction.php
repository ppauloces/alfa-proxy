<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'email',
        'transacao',
        'valor',
        'status',
        'metodo_pagamento',
        'tipo',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'valor' => 'decimal:2'
    ];
}
