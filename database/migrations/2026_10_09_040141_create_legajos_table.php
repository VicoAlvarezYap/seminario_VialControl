<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legajos', function (Blueprint $table) {
            $table->uuid('id_legajo')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('id_usuario')->unique();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->restrictOnDelete();
            $table->string('numero_legajo', 20)->unique();
            $table->date('fecha_ingreso');
            $table->string('cargo_puesto', 100)->nullable()->default('Agente Operativo');
            $table->text('observaciones')->nullable();
            $table->timestampTz('actualizado_en')->useCurrent();
        });

        // Trigger: actualiza actualizado_en en cada UPDATE
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_set_actualizado_en()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.actualizado_en := CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trg_legajos_actualizado
            BEFORE UPDATE ON legajos
            FOR EACH ROW EXECUTE FUNCTION fn_set_actualizado_en();
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('legajos');
        DB::statement('DROP FUNCTION IF EXISTS fn_set_actualizado_en() CASCADE');
    }
};