<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionHerramienta extends Model
{
    use HasUuids;

    protected $table = 'asignaciones_herramientas';
    protected $primaryKey = 'id_asignacion';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    // El id va en $fillable porque la app lo genera (soporte offline).
    protected $fillable = [
        'id_asignacion',
        'id_herramienta',
        'id_agente',
        'fecha_entrega',
        'fecha_devolucion',
        'observaciones_entrega',
        'observaciones_devolucion',
    ];

    protected $casts = [
        'fecha_entrega' => 'datetime',
        'fecha_devolucion' => 'datetime',
    ];

    public function herramienta(): BelongsTo
    {
        return $this->belongsTo(InventarioHerramienta::class, 'id_herramienta', 'id_herramienta');
    }

    public function agente(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'id_agente', 'id_usuario');
    }
}
