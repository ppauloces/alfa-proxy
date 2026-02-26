<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'cpf',
        'phone',
        'password',
        'username',
        'saldo',
        'cargo',
        'status',
        'xgate_customer_id',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

public function hasFeatureAccess($feature)
{
    if ($this->cargo === 'super') {
        return true;
    }

    return $this->adminPermissions()->where('feature', $feature)->exists();
}

public function stocks()
{
    return $this->hasMany(Stock::class, 'user_id');
}

/**
 * Relacionamento com cartões de crédito
 */
public function cartoes()
{
    return $this->hasMany(Cartao::class, 'user_id');
}

/**
 * Retorna o cartão padrão do usuário
 */
public function cartaoPadrao()
{
    return $this->hasOne(Cartao::class, 'user_id')->where('is_default', true);
}

/**
 * Verifica se o usuário é admin ou super admin
 */
public function isAdmin()
{
    return in_array($this->cargo, ['admin', 'super']);
}

/**
 * Verifica se o usuário é super admin
 */
public function isSuperAdmin()
{
    return $this->cargo === 'super';
}

/**
 * Verifica se o usuário é apenas usuário comum
 */
public function isUser()
{
    return $this->cargo === 'usuario';
}

/**
 * Verifica se o usuário é revendedor
 */
public function isRevendedor()
{
    return $this->cargo === 'revendedor';
}

/**
 * Retorna o preço base do proxy baseado no cargo do usuário
 */
public function getPrecoBase($periodo)
{
    $precosUsuario = [
        30 => 20.00,
        60 => 35.00,
        90 => 45.00,
        180 => 80.00,
        360 => 120.00,
    ];

    $precosRevendedor = [
        30 => 10.00,
        60 => 18.00,
        90 => 23.00,
        180 => 40.00,
        360 => 60.00,
    ];

    // Promoção até 02/02/2025 - apenas para usuários normais
    $dataLimitePromocao = \Carbon\Carbon::create(2026, 2, 2, 23, 59, 59);
    $emPromocao = now()->lte($dataLimitePromocao) && !$this->isRevendedor();

    if ($emPromocao) {
        $precosPromocao = [
            30 => 15.00,
            60 => 26.00,
            90 => 35.00,
            180 => 60.00,
            360 => 90.00,
        ];
        return $precosPromocao[$periodo] ?? 15.00;
    }

    if ($this->isRevendedor()) {
        return $precosRevendedor[$periodo] ?? 10.00;
    }

    return $precosUsuario[$periodo] ?? 20.00;
}

}
