<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuarios extends Authenticatable
{
    use HasApiTokens, HasUuids;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario', 'dni', 'nombre', 'apellido', 'telefono',
        'email', 'password_hash', 'rol', 'estado_activo',
    ];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'estado_activo' => 'boolean',
        'creado_en' => 'datetime',
        'password_hash' => 'hashed',
    ];

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    public function esDirector(): bool
    {
        return $this->rol === 'Director';
    }

    public function esAgente(): bool
    {
        return $this->rol === 'Agente';
    }
}