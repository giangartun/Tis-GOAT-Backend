<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantilla_proyecto', function (Blueprint $table) {
            $table->string('id_plantilla_proyecto')->primary();
            $table->string('id_plantilla');
            $table->string('id_portafolio');

            // Evita duplicados
            $table->unique(['id_plantilla', 'id_portafolio']);

            // Foreign Keys
            $table->foreign('id_plantilla')
                ->references('id_plantilla')
                ->on('plantilla')
                ->onDelete('cascade');

            $table->foreign('id_portafolio')
                ->references('id_portafolio')
                ->on('portafolio')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla_proyecto');
    }
};