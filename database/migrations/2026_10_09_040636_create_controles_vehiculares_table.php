<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('controles_vehiculares', function (Blueprint $table) {
            $table->uuid('id_control')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('id_agente');
            $table->string('patente_vehiculo', 15);
            $table->string('dni_conductor', 15)->nullable();
            $table->string('nombre_conductor', 100)->nullable();
            $table->text('motivo_detencion');
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->text('foto_evidencia_url')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestampTz('fecha_hora');
            $table->timestampTz('recibido_en')->useCurrent();
            $table->foreign('id_agente')->references('id_usuario')->on('usuarios')->restrictOnDelete();
        });
        //Posible trigger: los controles no se pueden modificar pero que pasa
        // si se tiene un error en el dni o otro error 
        // a cuestionar 
        DB::statement('ALTER TABLE controles_vehiculares ADD CONSTRAINT ck_controles_vehiculares_latitud CHECK (latitud BETWEEN -90 AND 90)');
        DB::statement('ALTER TABLE controles_vehiculares ADD CONSTRAINT ck_controles_vehiculares_longitud CHECK (longitud BETWEEN -180 AND 180)');
        DB::statement("ALTER TABLE controles_vehiculares ADD CONSTRAINT ck_controles_vehiculares_fecha CHECK (fecha_hora <= recibido_en + INTERVAL '5 minutes')");
        DB::statement('CREATE INDEX idx_controles_agente_fecha ON controles_vehiculares (id_agente, fecha_hora DESC)');
        DB::statement('CREATE INDEX idx_controles_fecha ON controles_vehiculares (fecha_hora)');
        DB::statement('CREATE INDEX idx_controles_patente ON controles_vehiculares (patente_vehiculo)');
        DB::statement('CREATE INDEX idx_controles_dni_conductor ON controles_vehiculares (dni_conductor) WHERE dni_conductor IS NOT NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('controles_vehiculares');
    }
};
