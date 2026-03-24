<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyecto', function (Blueprint $table) {
            $table->string('id_proyecto')->primary();

            $table->string('nombre');
            $table->text('descripcion')->nullable();

            $table->string('url_repositorio')->nullable();
            $table->string('url_demo')->nullable();
            $table->string('imagen_url')->nullable();

            $table->date('fecha_ini')->nullable();
            $table->date('fecha_fin')->nullable();

            $table->boolean('visible')->default(true);
            $table->timestamp('fecha_creado')->nullable();

            $table->string('id_portafolio');

            $table->foreign('id_portafolio')
                ->references('id_portafolio')
                ->on('portafolio')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto');
    }
};