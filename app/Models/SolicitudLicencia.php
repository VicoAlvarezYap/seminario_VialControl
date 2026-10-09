<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudLicencia extends Model
{
    use HasUuids;

    protected $table = 'solicitudes_licencias';
    protected $primaryKey = 'id_solicitud';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    // El id va en $fillable porque la app lo genera (soporte offline).
    protected $fillable = [
        'id_solicitud',
        'id_agente',
        'tipo_solicitud',
        'motivo',
        'fecha_inicio',
        'fecha_fin',
        'comprobante_url',
        'estado',
        'id_director_respuesta',
        'respuesta_comentario',
        'fecha_respuesta',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_respuesta' => 'datetime',
        'creado_en' => 'datetime',
    ];

    public function agente(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'id_agente', 'id_usuario');
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'id_director_respuesta', 'id_usuario');
    }
}
