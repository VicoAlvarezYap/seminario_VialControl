<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registros_velocidad', function (Blueprint $table) {
            $table->uuid('id_registro_velocidad')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('id_agente');
            $table->decimal('velocidad_kmh', 5, 2);
            $table->string('patente_vehiculo', 15)->nullable();
            $table->text('foto_captura_url');
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->timestampTz('fecha_hora');
            $table->timestampTz('recibido_en')->useCurrent();
            $table->foreign('id_agente')->references('id_usuario')->on('usuarios')->restrictOnDelete();
        });

    
        DB::statement('ALTER TABLE registros_velocidad ADD CONSTRAINT ck_registros_velocidad_valor CHECK (velocidad_kmh > 0)');
        DB::statement('ALTER TABLE registros_velocidad ADD CONSTRAINT ck_registros_velocidad_latitud CHECK (latitud BETWEEN -90 AND 90)');
        DB::statement('ALTER TABLE registros_velocidad ADD CONSTRAINT ck_registros_velocidad_longitud CHECK (longitud BETWEEN -180 AND 180)');
        DB::statement("ALTER TABLE registros_velocidad ADD CONSTRAINT ck_registros_velocidad_fecha CHECK (fecha_hora <= recibido_en + INTERVAL '5 minutes')");

        DB::statement('CREATE INDEX idx_velocidad_agente_fecha ON registros_velocidad (id_agente, fecha_hora DESC)');
        DB::statement('CREATE INDEX idx_velocidad_fecha ON registros_velocidad (fecha_hora)');
        DB::statement('CREATE INDEX idx_velocidad_patente ON registros_velocidad (patente_vehiculo) WHERE patente_vehiculo IS NOT NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_velocidad');
    }
};