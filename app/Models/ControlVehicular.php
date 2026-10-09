<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlVehicular extends Model
{
    use HasUuids;

    protected $table = 'controles_vehiculares';
    protected $primaryKey = 'id_control';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    // El id va en $fillable porque la app lo genera (soporte offline).
    protected $fillable = [
        'id_control',
        'id_agente',
        'patente_vehiculo',
        'dni_conductor',
        'nombre_conductor',
        'motivo_detencion',
        'latitud',
        'longitud',
        'foto_evidencia_url',
        'observaciones',
        'fecha_hora',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'recibido_en' => 'datetime',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function agente(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'id_agente', 'id_usuario');
    }
}
