<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anuncio', function (Blueprint $table) {
            $table->string('id_anuncio')->primary();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('foto_url')->nullable();
            $table->string('url_redireccion');
            $table->timestamp('creado_en')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anuncio');
    }
};