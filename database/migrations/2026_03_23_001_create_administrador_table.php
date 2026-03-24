<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrador', function (Blueprint $table) {
            $table->string('id_administrador')->primary();
            $table->string('correo')->unique();
            $table->string('contraseña');
            $table->string('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrador');
    }
};