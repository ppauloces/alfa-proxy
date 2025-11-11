<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupom extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'cupom',
        'desconto',
        'quantidade',
        'minimo',
        'maximo',
    ];
}
