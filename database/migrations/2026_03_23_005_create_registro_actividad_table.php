<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registro_actividad', function (Blueprint $table) {
            $table->string('id_registro')->primary();
            $table->string('id_usuario');
            $table->string('evento');
            $table->timestamp('fecha_hr')->nullable();
            $table->json('contexto')->nullable();

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuario')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registro_actividad');
    }
};