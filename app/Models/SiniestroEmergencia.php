<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiniestroEmergencia extends Model
{
    use HasUuids;

    protected $table = 'siniestros_emergencias';
    protected $primaryKey = 'id_siniestro';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    // El id va en $fillable porque la app lo genera (soporte offline).
    protected $fillable = [
        'id_siniestro',
        'id_agente_reporta',
        'tipo_evento',
        'nivel_gravedad',
        'ubicacion_descripcion',
        'latitud',
        'longitud',
        'recursos_desplegados',
        'foto_evidencia_url',
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
        return $this->belongsTo(Usuarios::class, 'id_agente_reporta', 'id_usuario');
    }
}
