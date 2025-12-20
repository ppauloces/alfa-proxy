<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cartao extends Model
{
    protected $fillable = [
        'user_id',
        'bandeira',
        'ultimos_digitos',
        'mes_expiracao',
        'ano_expiracao',
        'nome_titular',
        'cpf',
        'gateway',
        'token_gateway1',
        'token_gateway2',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'mes_expiracao' => 'integer',
        'ano_expiracao' => 'integer',
    ];

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se o cartão está expirado
     */
    public function isExpired(): bool
    {
        $now = now();
        $expirationDate = \Carbon\Carbon::createFromDate($this->ano_expiracao, $this->mes_expiracao, 1)->endOfMonth();

        return $now->greaterThan($expirationDate);
    }

    /**
     * Formata a validade do cartão
     */
    public function getFormattedExpiryAttribute(): string
    {
        return str_pad($this->mes_expiracao, 2, '0', STR_PAD_LEFT) . '/' . $this->ano_expiracao;
    }

    /**
     * Retorna o número mascarado do cartão
     */
    public function getMaskedNumberAttribute(): string
    {
        return '•••• •••• •••• ' . $this->ultimos_digitos;
    }

    /**
     * Retorna o CPF mascarado
     */
    public function getMaskedCpfAttribute(): string
    {
        if (!$this->cpf) {
            return '';
        }

        // Remove caracteres não numéricos
        $cpf = preg_replace('/\D/', '', $this->cpf);

        // Mascara: ***.123.456-**
        return '•••.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-••';
    }
}
