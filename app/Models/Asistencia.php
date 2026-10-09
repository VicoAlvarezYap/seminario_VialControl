<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    use HasUuids;

    protected $table = 'asistencias';
    protected $primaryKey = 'id_asistencia';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    // El id va en $fillable porque la app lo genera (soporte offline).
    protected $fillable = [
        'id_asistencia',
        'id_usuario',
        'tipo_marca',
        'fecha_hora',
        'latitud',
        'longitud',
        'foto_validacion_url',
        'observaciones',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'recibido_en' => 'datetime',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }
}
