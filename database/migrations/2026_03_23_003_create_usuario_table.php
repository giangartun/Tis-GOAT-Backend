<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->string('id_usuario')->primary(); // ULID
            $table->string('email')->unique();
            $table->string('contrasena');
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->text('biografia')->nullable();
            $table->string('foto')->nullable();
            $table->timestamp('fecha')->nullable(); // Fecha y hora de creación automática
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};