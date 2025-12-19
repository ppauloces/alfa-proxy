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
        'bloqueada'
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
    ];

}
