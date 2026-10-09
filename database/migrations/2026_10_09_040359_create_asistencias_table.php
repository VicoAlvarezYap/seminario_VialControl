<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->uuid('id_asistencia')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('id_usuario');
            $table->string('tipo_marca', 10);
            $table->timestampTz('fecha_hora');
            $table->timestampTz('recibido_en')->useCurrent();
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->text('foto_validacion_url');
            $table->text('observaciones')->nullable();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->restrictOnDelete();
        });

        // DUda
        DB::statement("ALTER TABLE asistencias ADD CONSTRAINT ck_asistencias_tipo CHECK (tipo_marca IN ('Ingreso', 'Salida'))");
        DB::statement('ALTER TABLE asistencias ADD CONSTRAINT ck_asistencias_latitud CHECK (latitud BETWEEN -90 AND 90)');
        DB::statement('ALTER TABLE asistencias ADD CONSTRAINT ck_asistencias_longitud CHECK (longitud BETWEEN -180 AND 180)');
        DB::statement("ALTER TABLE asistencias ADD CONSTRAINT ck_asistencias_fecha CHECK (fecha_hora <= recibido_en + INTERVAL '5 minutes')");

    
        DB::statement('CREATE INDEX idx_asistencias_usuario_fecha ON asistencias (id_usuario, fecha_hora DESC)');
        DB::statement('CREATE INDEX idx_asistencias_fecha ON asistencias (fecha_hora)');
        DB::statement('CREATE INDEX idx_asistencias_coordenadas ON asistencias (latitud, longitud)');
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
