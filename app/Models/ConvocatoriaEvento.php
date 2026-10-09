<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConvocatoriaEvento extends Model
{
    use HasUuids;

    protected $table = 'convocatorias_eventos';
    protected $primaryKey = 'id_convocatoria';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    // El id va en $fillable porque la app lo genera (soporte offline).
    protected $fillable = [
        'id_convocatoria',
        'id_evento',
        'id_agente',
        'asistio',
        'confirmado',
    ];

    protected $casts = [
        'asistio' => 'boolean',
        'confirmado' => 'boolean',
    ];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(EventoCapacitacion::class, 'id_evento', 'id_evento');
    }

    public function agente(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'id_agente', 'id_usuario');
    }
}
