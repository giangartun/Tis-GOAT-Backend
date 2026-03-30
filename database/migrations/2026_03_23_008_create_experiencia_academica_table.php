<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiencia_academica', function (Blueprint $table) {
            $table->string('id_experiencia_academica')->primary();
            $table->string('institucion');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_ini');
            $table->date('fecha_fin')->nullable();
            $table->string('id_portafolio');
            
            $table->foreign('id_portafolio')
                ->references('id_portafolio')
                ->on('portafolio')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiencia_academica');
    }
};