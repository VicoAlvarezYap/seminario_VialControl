<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MensajeInstitucional extends Model
{
    use HasUuids;

    protected $table = 'mensajes_institucionales';
    protected $primaryKey = 'id_mensaje';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    // El id va en $fillable porque la app lo genera (soporte offline).
    protected $fillable = [
        'id_mensaje',
        'id_emisor',
        'id_receptor',
        'asunto',
        'contenido',
        'leido',
    ];

    protected $casts = [
        'leido' => 'boolean',
        'fecha_envio' => 'datetime',
    ];

    public function emisor(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'id_emisor', 'id_usuario');
    }

    public function receptor(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'id_receptor', 'id_usuario');
    }
}
