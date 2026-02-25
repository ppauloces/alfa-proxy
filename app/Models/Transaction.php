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
        'gateway_transaction_id',
        'payment_method',
        'card_id',
        'valor',
        'status',
        'metodo_pagamento',
        'tipo',
        'metadata',
        'stock_ids',
    ];

    protected $casts = [
        'metadata' => 'array',
        'stock_ids' => 'array',
        'valor' => 'decimal:2',
    ];

    /**
     * Relacionamento com o cartão usado na transação
     */
    public function card()
    {
        return $this->belongsTo(Cartao::class, 'card_id');
    }

    /**
     * Relacionamento com o usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
