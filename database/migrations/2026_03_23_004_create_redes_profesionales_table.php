<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redes_profesionales', function (Blueprint $table) {
            $table->string('id_redes_prof')->primary();
            $table->string('id_usuario');
            $table->string('nombre_red');
            $table->string('url_red');
            $table->boolean('visible')->default(true);

            // Foreign Key
            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuario')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redes_profesionales');
    }
};