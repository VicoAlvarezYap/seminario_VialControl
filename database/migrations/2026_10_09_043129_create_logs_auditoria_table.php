<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_auditoria', function (Blueprint $table) {
            $table->uuid('id_log')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('id_usuario')->nullable();
            $table->string('tabla_afectada', 50);
            $table->string('operacion', 10);
            $table->jsonb('detalles');
            $table->timestampTz('fecha_hora')->useCurrent();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->nullOnDelete();
        });

        //restricciones ver
        DB::statement("ALTER TABLE logs_auditoria ADD CONSTRAINT ck_logs_operacion CHECK (operacion IN ('INSERT', 'UPDATE', 'DELETE'))");

        // Trigger: la tabla es de solo inserción (no se puede editar ni borrar)
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_logs_inmutables()
            RETURNS TRIGGER AS $$
            BEGIN
                IF TG_OP = 'UPDATE'
                   AND NEW.id_usuario IS NULL
                   AND NEW.id_log = OLD.id_log
                   AND NEW.tabla_afectada = OLD.tabla_afectada
                   AND NEW.operacion = OLD.operacion
                   AND NEW.detalles = OLD.detalles
                   AND NEW.fecha_hora = OLD.fecha_hora THEN
                    RETURN NEW;
                END IF;
                RAISE EXCEPTION 'logs_auditoria es de solo inserción (% no permitido)', TG_OP;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trg_logs_inmutables
            BEFORE UPDATE OR DELETE ON logs_auditoria
            FOR EACH ROW EXECUTE FUNCTION fn_logs_inmutables();
        SQL);

        DB::statement('CREATE INDEX idx_logs_tabla_fecha ON logs_auditoria (tabla_afectada, fecha_hora DESC)');
        DB::statement('CREATE INDEX idx_logs_usuario_fecha ON logs_auditoria (id_usuario, fecha_hora DESC)');
        DB::statement('CREATE INDEX idx_logs_detalles_gin ON logs_auditoria USING GIN (detalles)');
    }

    public function down(): void
    {
        DB::statement('DROP FUNCTION IF EXISTS fn_logs_inmutables() CASCADE');
        Schema::dropIfExists('logs_auditoria');
    }
};
