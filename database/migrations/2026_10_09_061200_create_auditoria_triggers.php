<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Auditoría automática: registra los cambios en logs_auditoria
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_auditar()
            RETURNS TRIGGER AS $$
            DECLARE
                v_user_txt TEXT := NULLIF(current_setting('app.user_id', true), '');
                v_user     UUID;
                v_detalle  JSONB;
            BEGIN
                IF v_user_txt IS NOT NULL THEN
                    v_user := v_user_txt::UUID;
                END IF;

                IF TG_OP = 'INSERT' THEN
                    v_detalle := jsonb_build_object('nuevo', to_jsonb(NEW));
                ELSIF TG_OP = 'UPDATE' THEN
                    v_detalle := jsonb_build_object('anterior', to_jsonb(OLD), 'nuevo', to_jsonb(NEW));
                ELSE
                    v_detalle := jsonb_build_object('anterior', to_jsonb(OLD));
                END IF;

                -- Nunca guardar hashes de contraseña en el log
                IF TG_TABLE_NAME = 'usuarios' THEN
                    v_detalle := v_detalle #- '{nuevo,password_hash}' #- '{anterior,password_hash}';
                END IF;

                INSERT INTO logs_auditoria (id_usuario, tabla_afectada, operacion, detalles)
                VALUES (v_user, TG_TABLE_NAME, TG_OP, v_detalle);

                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trg_audit_usuarios AFTER INSERT OR UPDATE OR DELETE ON usuarios FOR EACH ROW EXECUTE FUNCTION fn_auditar();
            CREATE TRIGGER trg_audit_legajos AFTER INSERT OR UPDATE OR DELETE ON legajos FOR EACH ROW EXECUTE FUNCTION fn_auditar();
            CREATE TRIGGER trg_audit_asistencias AFTER INSERT OR UPDATE OR DELETE ON asistencias FOR EACH ROW EXECUTE FUNCTION fn_auditar();
            CREATE TRIGGER trg_audit_solicitudes AFTER INSERT OR UPDATE OR DELETE ON solicitudes_licencias FOR EACH ROW EXECUTE FUNCTION fn_auditar();
            CREATE TRIGGER trg_audit_controles AFTER INSERT OR UPDATE OR DELETE ON controles_vehiculares FOR EACH ROW EXECUTE FUNCTION fn_auditar();
            CREATE TRIGGER trg_audit_velocidad AFTER INSERT OR UPDATE OR DELETE ON registros_velocidad FOR EACH ROW EXECUTE FUNCTION fn_auditar();
            CREATE TRIGGER trg_audit_siniestros AFTER INSERT OR UPDATE OR DELETE ON siniestros_emergencias FOR EACH ROW EXECUTE FUNCTION fn_auditar();
            CREATE TRIGGER trg_audit_inventario AFTER INSERT OR UPDATE OR DELETE ON inventario_herramientas FOR EACH ROW EXECUTE FUNCTION fn_auditar();
            CREATE TRIGGER trg_audit_asignaciones AFTER INSERT OR UPDATE OR DELETE ON asignaciones_herramientas FOR EACH ROW EXECUTE FUNCTION fn_auditar();
            CREATE TRIGGER trg_audit_eventos AFTER INSERT OR UPDATE OR DELETE ON eventos_capacitaciones FOR EACH ROW EXECUTE FUNCTION fn_auditar();
            CREATE TRIGGER trg_audit_convocatorias AFTER INSERT OR UPDATE OR DELETE ON convocatorias_eventos FOR EACH ROW EXECUTE FUNCTION fn_auditar();
        SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_audit_usuarios ON usuarios');
        DB::statement('DROP TRIGGER IF EXISTS trg_audit_legajos ON legajos');
        DB::statement('DROP TRIGGER IF EXISTS trg_audit_asistencias ON asistencias');
        DB::statement('DROP TRIGGER IF EXISTS trg_audit_solicitudes ON solicitudes_licencias');
        DB::statement('DROP TRIGGER IF EXISTS trg_audit_controles ON controles_vehiculares');
        DB::statement('DROP TRIGGER IF EXISTS trg_audit_velocidad ON registros_velocidad');
        DB::statement('DROP TRIGGER IF EXISTS trg_audit_siniestros ON siniestros_emergencias');
        DB::statement('DROP TRIGGER IF EXISTS trg_audit_inventario ON inventario_herramientas');
        DB::statement('DROP TRIGGER IF EXISTS trg_audit_asignaciones ON asignaciones_herramientas');
        DB::statement('DROP TRIGGER IF EXISTS trg_audit_eventos ON eventos_capacitaciones');
        DB::statement('DROP TRIGGER IF EXISTS trg_audit_convocatorias ON convocatorias_eventos');
        DB::statement('DROP FUNCTION IF EXISTS fn_auditar() CASCADE');
    }
};
