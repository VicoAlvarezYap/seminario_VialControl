<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->uuid('id_usuario')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('dni', 15)->unique();
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('telefono', 25)->nullable();
            $table->string('email', 100);
            $table->string('password_hash', 255);
            $table->string('rol', 20);
            $table->boolean('estado_activo')->default(true);
            $table->timestampTz('creado_en')->useCurrent();
        });

        DB::statement("ALTER TABLE usuarios ADD CONSTRAINT ck_usuarios_rol CHECK (rol IN ('Director', 'Agente'))");
        DB::statement('CREATE UNIQUE INDEX uq_usuarios_email_lower ON usuarios (LOWER(email))');
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};