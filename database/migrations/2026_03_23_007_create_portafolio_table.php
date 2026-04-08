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
            
            // Regla: 1 Usuario = 1 Portafolio
            $table->string('id_usuario')->unique(); 
            $table->string('id_plantilla')->nullable();
            
            $table->string('enlace_pagi_web')->unique();
            
            // Usamos timestamp para mayor precisión en Neon.tech
            $table->timestamp('creado_en')->nullable();
            $table->timestamp('fecha_act')->nullable();

        // Llaves foráneas
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');
            $table->foreign('id_plantilla')->references('id_plantilla')->on('plantilla')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portafolio');
    }
};