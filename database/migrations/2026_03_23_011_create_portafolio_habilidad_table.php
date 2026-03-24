<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portafolio_habilidad', function (Blueprint $table) {
            $table->string('id_portafolio_habilidad')->primary();
            $table->string('id_portafolio');
            $table->string('id_habilidad');

            // Evita duplicados
            $table->unique(['id_portafolio', 'id_habilidad']);

            // Foreign Keys
            $table->foreign('id_portafolio')
                ->references('id_portafolio')
                ->on('portafolio')
                ->onDelete('cascade');

            $table->foreign('id_habilidad')
                ->references('id_habilidad')
                ->on('habilidad')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portafolio_habilidad');
    }
};