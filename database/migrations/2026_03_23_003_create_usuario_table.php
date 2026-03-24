<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->string('id_usuario')->primary();
            $table->string('correo')->unique();
            $table->string('contrasena');
            $table->string('nombre');
            $table->string('profesion')->nullable();
            $table->text('biografia')->nullable();
            $table->string('foto_url')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creado')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};