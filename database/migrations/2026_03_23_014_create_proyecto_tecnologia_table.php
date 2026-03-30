<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyecto_tecnologia', function (Blueprint $table) {
            $table->string('id_proyecto_tecnologia')->primary();
            $table->string('id_proyecto');
            $table->string('id_tecnologia');
            $table->string('tipo')->nullable();

            // Evita duplicados (misma relación repetida)
            $table->unique(['id_proyecto', 'id_tecnologia', 'tipo'], 'proy_tec_tipo_unique');

            // Foreign Keys
            $table->foreign('id_proyecto')
                ->references('id_proyecto')
                ->on('proyecto')
                ->onDelete('cascade');

            $table->foreign('id_tecnologia')
                ->references('id_tecnologia')
                ->on('tecnologia')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_tecnologia');
    }
};