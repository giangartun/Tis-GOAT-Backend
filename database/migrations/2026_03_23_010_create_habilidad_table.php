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
            
            $table->string('id_portafolio');
            
            $table->string('nombre');
            $table->string('tipo');       // "tecnica" | "blanda"
            $table->string('categoria');  // "Lenguajes de programación", "Frameworks y Librerías", etc.
            $table->integer('nivel');     // 0 al 100
            $table->boolean('visible')->default(true);

            $table->foreign('id_portafolio')
                ->references('id_portafolio')
                ->on('portafolio')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habilidad');
    }
};