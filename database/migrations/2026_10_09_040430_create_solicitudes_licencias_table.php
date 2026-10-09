<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_licencias', function (Blueprint $table) {
            $table->uuid('id_solicitud')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('id_agente');
            $table->string('tipo_solicitud', 50);
            $table->text('motivo');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->text('comprobante_url')->nullable();
            $table->string('estado', 20)->default('Pendiente');
            $table->uuid('id_director_respuesta')->nullable();
            $table->text('respuesta_comentario')->nullable();
            $table->timestampTz('fecha_respuesta')->nullable();
            $table->timestampTz('creado_en')->useCurrent();
            $table->foreign('id_agente')->references('id_usuario')->on('usuarios')->restrictOnDelete();
            $table->foreign('id_director_respuesta')->references('id_usuario')->on('usuarios')->restrictOnDelete();
        });

        
        DB::statement("ALTER TABLE solicitudes_licencias ADD CONSTRAINT ck_solicitud_estado CHECK (estado IN ('Pendiente', 'Aprobado', 'Rechazado'))");
        DB::statement('ALTER TABLE solicitudes_licencias ADD CONSTRAINT ck_solicitud_fechas CHECK (fecha_fin >= fecha_inicio)');
        DB::statement("ALTER TABLE solicitudes_licencias ADD CONSTRAINT ck_solicitud_respuesta CHECK ((estado = 'Pendiente' AND id_director_respuesta IS NULL AND fecha_respuesta IS NULL) OR (estado <> 'Pendiente' AND id_director_respuesta IS NOT NULL AND fecha_respuesta IS NOT NULL))");
        // capaz falte otro trigger mas
        // Trigger: un solo usuario habilita el pedido y solo un usurio acepta
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_validar_solicitud()
            RETURNS TRIGGER AS $$
            DECLARE
                v_rol VARCHAR(20);
            BEGIN
                SELECT rol INTO v_rol FROM usuarios WHERE id_usuario = NEW.id_agente;
                IF v_rol IS DISTINCT FROM 'Agente' THEN
                    RAISE EXCEPTION 'El solicitante (%) debe tener rol Agente', NEW.id_agente;
                END IF;

                IF NEW.id_director_respuesta IS NOT NULL THEN
                    SELECT rol INTO v_rol FROM usuarios
                    WHERE id_usuario = NEW.id_director_respuesta AND estado_activo = TRUE;
                    IF v_rol IS DISTINCT FROM 'Director' THEN
                        RAISE EXCEPTION 'El usuario % no es un Director activo', NEW.id_director_respuesta;
                    END IF;
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trg_solicitudes_validar
            BEFORE INSERT OR UPDATE ON solicitudes_licencias
            FOR EACH ROW EXECUTE FUNCTION fn_validar_solicitud();
        SQL);

        
        DB::statement('CREATE INDEX idx_solicitudes_agente_estado ON solicitudes_licencias (id_agente, estado)');
        DB::statement('CREATE INDEX idx_solicitudes_estado_fecha ON solicitudes_licencias (estado, creado_en DESC)');
        DB::statement('CREATE INDEX idx_solicitudes_director ON solicitudes_licencias (id_director_respuesta) WHERE id_director_respuesta IS NOT NULL');
    }

    public function down(): void
    {
        DB::statement('DROP FUNCTION IF EXISTS fn_validar_solicitud() CASCADE');
        Schema::dropIfExists('solicitudes_licencias');
    }
};
