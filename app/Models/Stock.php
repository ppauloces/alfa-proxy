<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = ['user_id', 'vps_id', 'tipo', 'ip', 'porta', 'usuario', 'senha', 'pais', 'expiracao', 'disponibilidade'];




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
    ];

}
