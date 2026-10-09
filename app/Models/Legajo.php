<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Legajo extends Model
{
    use HasUuids;

    protected $table = 'legajos';
    protected $primaryKey = 'id_legajo';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    // El id va en $fillable porque la app lo genera (soporte offline).
    protected $fillable = [
        'id_legajo',
        'id_usuario',
        'numero_legajo',
        'fecha_ingreso',
        'cargo_puesto',
        'observaciones',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'actualizado_en' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }
}
