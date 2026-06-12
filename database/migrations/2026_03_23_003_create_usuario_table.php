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
            $table->enum('tipo_usuario', ['admin', 'usuario'])->default('usuario');
            $table->string('email')->unique();
            $table->string('contrasena');
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->text('biografia')->nullable();
            $table->enum('estado_cuenta', ['activo', 'suspendido'])->default('activo');
            $table->string('foto')->nullable();
            $table->timestamp('fecha')->nullable();
            $table->timestamp('fecha_ult_acceso')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};