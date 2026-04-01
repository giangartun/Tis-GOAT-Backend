<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiencia_academica_grado', function (Blueprint $table) {

            $table->string('id_experiencia_academica');
            $table->string('id_grado');

            $table->foreign('id_experiencia_academica')
                ->references('id_experiencia_academica')
                ->on('experiencia_academica')
                ->onDelete('cascade');

            $table->foreign('id_grado')
                ->references('id_grado')
                ->on('grado')
                ->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiencia_academica_grado');
    }
};