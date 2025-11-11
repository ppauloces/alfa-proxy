<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = ['tipo', 'ip', 'porta', 'usuario', 'senha', 'disponibilidade'];


    

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    protected $casts = [
    'expiracao' => 'datetime',
];

}
