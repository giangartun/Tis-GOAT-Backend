<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencia', function (Blueprint $table) {
            $table->string('id_evidencia')->primary();
            $table->string('tipo');
            $table->string('url_evidencia');
            $table->string('nombre_archivo');
            $table->boolean('foto_url')->default(false);
            $table->bigInteger('tamano_bytes')->nullable();
            $table->timestamp('fecha_subida')->nullable();
            $table->string('id_proyecto');

            $table->foreign('id_proyecto')
                ->references('id_proyecto')
                ->on('proyecto')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencia');
    }
};