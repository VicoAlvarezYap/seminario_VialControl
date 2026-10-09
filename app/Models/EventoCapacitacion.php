<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventoCapacitacion extends Model
{
    use HasUuids;

    protected $table = 'eventos_capacitaciones';
    protected $primaryKey = 'id_evento';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    // El id va en $fillable porque la app lo genera (soporte offline).
    protected $fillable = [
        'id_evento',
        'titulo',
        'descripcion',
        'tipo_evento',
        'fecha_inicio',
        'fecha_fin',
        'lugar',
        'id_creador',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'id_creador', 'id_usuario');
    }

    public function convocatorias(): HasMany
    {
        return $this->hasMany(ConvocatoriaEvento::class, 'id_evento', 'id_evento');
    }

    /** Agentes convocados (tabla pivote convocatorias_eventos). */
    public function agentes(): BelongsToMany
    {
        return $this->belongsToMany(Usuarios::class, 'convocatorias_eventos', 'id_evento', 'id_agente')
            ->withPivot('asistio', 'confirmado');
    }
}
