<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('habilidad', function (Blueprint $table) {
            $table->string('id_habilidad')->primary();
            
            // 1. Añadimos el conector con el Portafolio
            $table->string('id_portafolio'); 
            
            $table->string('nombre');
            $table->string('tipo'); // Aquí guardarás "Dura" o "Blanda"
            $table->integer('nivel');
            $table->boolean('visible')->default(true);

            // 2. Definimos la relación oficial
            $table->foreign('id_portafolio')
            ->references('id_portafolio')
            ->on('portafolio')
            ->onDelete('cascade'); // Si borras el portafolio, se borran sus habilidades
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habilidad');
    }
};
