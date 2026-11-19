<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'dni', 'name', 'email', 'password', 'rol', 'estado',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function psicologo(): HasOne
    {
        return $this->hasOne(Psicologo::class);
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function esPsicologo(): bool
    {
        return $this->rol === 'psicologo';
    }

    public function esRecepcionista(): bool
    {
        return $this->rol === 'recepcionista';
    }
}