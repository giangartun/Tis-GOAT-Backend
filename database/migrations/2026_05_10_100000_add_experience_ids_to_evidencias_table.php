<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('evidencia', function (Blueprint $table) {
            // 1. Permitimos que id_proyecto sea nulo (opcional)
            $table->string('id_proyecto', 26)->nullable()->change();

            // 2. Creamos los nuevos campos para las otras tablas
            $table->string('id_experiencia_academica', 26)->nullable();
            $table->string('id_experiencia_laboral', 26)->nullable();

            // 3. Creamos las relaciones (Foreign Keys)
            $table->foreign('id_experiencia_academica')
                  ->references('id_experiencia_academica')
                  ->on('experiencia_academica') // Verifica si tu tabla se llama así o 'experiencia_academica'
                  ->onDelete('cascade');

            $table->foreign('id_experiencia_laboral')
                  ->references('id_experiencia')
                  ->on('experiencia_laboral') // Verifica si se llama 'experiencia_laboral'
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('evidencia', function (Blueprint $table) {
            $table->dropForeign(['id_experiencia_academica']);
            $table->dropForeign(['id_experiencia_laboral']);
            $table->dropColumn(['id_experiencia_academica', 'id_experiencia_laboral']);
            $table->string('id_proyecto', 26)->nullable(false)->change();
        });
    }
};