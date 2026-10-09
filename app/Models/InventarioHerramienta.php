<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventarioHerramienta extends Model
{
    use HasUuids;

    protected $table = 'inventario_herramientas';
    protected $primaryKey = 'id_herramienta';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    // El id va en $fillable porque la app lo genera (soporte offline).
    protected $fillable = [
        'id_herramienta',
        'codigo_inventario',
        'nombre',
        'categoria',
        'estado_conservacion',
    ];

    protected $casts = [
        'disponible' => 'boolean',
    ];

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionHerramienta::class, 'id_herramienta', 'id_herramienta');
    }
}
