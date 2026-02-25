<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'user_id',
        'vps_id',
        'tipo',
        'ip',
        'porta',
        'usuario',
        'senha',
        'pais',
        'codigo_pais',
        'motivo_uso',
        'periodo_dias',
        'expiracao',
        'disponibilidade',
        'renovacao_automatica',
        'bloqueada',
        'uso_interno',
        'finalidade_interna',
        'substituido',
        'substituido_por',
        'reembolsada',
        'reembolsado_por',
        'reembolsado_em',
    ];




    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vps()
    {
        return $this->belongsTo(Vps::class);
    }


    protected $casts = [
        'expiracao' => 'datetime',
        'bloqueada' => 'boolean',
        'uso_interno' => 'boolean',
        'substituido' => 'boolean',
        'reembolsada' => 'boolean',
        'reembolsado_em' => 'datetime',
    ];

    public function substituto()
    {
        return $this->belongsTo(Stock::class, 'substituido_por');
    }

}
