<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiencia_laboral', function (Blueprint $table) {
            $table->string('id_experiencia')->primary();
            $table->string('empresa');
            $table->string('cargo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_ini');
            $table->date('fecha_fin')->nullable();
            $table->boolean('actual')->default(false);
            $table->boolean('visible')->default(true);
            $table->string('id_portafolio');

            $table->foreign('id_portafolio')
                ->references('id_portafolio')
                ->on('portafolio')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiencia_laboral');
    }
};