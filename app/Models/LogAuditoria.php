<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Solo lectura: los logs los inserta PostgreSQL mediante triggers. */
class LogAuditoria extends Model
{
    use HasUuids;

    protected $table = 'logs_auditoria';
    protected $primaryKey = 'id_log';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $guarded = ['*'];

    protected $casts = [
        'detalles' => 'array',
        'fecha_hora' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }
}
