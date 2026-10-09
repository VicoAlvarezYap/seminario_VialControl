<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes_institucionales', function (Blueprint $table) {
            $table->uuid('id_mensaje')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('id_emisor');
            $table->uuid('id_receptor');
            $table->string('asunto', 150)->nullable();
            $table->text('contenido');
            $table->boolean('leido')->default(false);
            $table->timestampTz('fecha_envio')->useCurrent();
            $table->foreign('id_emisor')->references('id_usuario')->on('usuarios')->restrictOnDelete();
            $table->foreign('id_receptor')->references('id_usuario')->on('usuarios')->restrictOnDelete();
        });

        // restriccion
        DB::statement('ALTER TABLE mensajes_institucionales ADD CONSTRAINT ck_mensaje_distintos CHECK (id_emisor <> id_receptor)');
        DB::statement('CREATE INDEX idx_mensajes_receptor_fecha ON mensajes_institucionales (id_receptor, fecha_envio DESC)');
        DB::statement('CREATE INDEX idx_mensajes_emisor_fecha ON mensajes_institucionales (id_emisor, fecha_envio DESC)');
        DB::statement('CREATE INDEX idx_mensajes_no_leidos ON mensajes_institucionales (id_receptor) WHERE leido = FALSE');
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes_institucionales');
    }
};
