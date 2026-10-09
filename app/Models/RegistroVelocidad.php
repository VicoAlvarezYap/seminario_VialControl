<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroVelocidad extends Model
{
    use HasUuids;

    protected $table = 'registros_velocidad';
    protected $primaryKey = 'id_registro_velocidad';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    // El id va en $fillable porque la app lo genera (soporte offline).
    protected $fillable = [
        'id_registro_velocidad',
        'id_agente',
        'velocidad_kmh',
        'patente_vehiculo',
        'foto_captura_url',
        'latitud',
        'longitud',
        'fecha_hora',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'recibido_en' => 'datetime',
        'velocidad_kmh' => 'decimal:2',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function agente(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'id_agente', 'id_usuario');
    }
}
