<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portafolio', function (Blueprint $table) {
            $table->string('id_portafolio')->primary();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->boolean('publicado')->default(false);
            $table->string('slug')->unique();
            $table->timestamp('fecha_creado')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();
            $table->string('id_usuario');

            // Foreign Key
            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuario')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portafolio');
    }
};