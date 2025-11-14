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
        'password',
        'username',
        'saldo',
        'cargo',
        'status'
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

}
