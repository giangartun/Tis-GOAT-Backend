<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habilidad', function (Blueprint $table) {
            $table->string('id_habilidad')->primary();
            $table->string('nombre');
            $table->string('tipo');
            $table->integer('nivel');
            $table->boolean('visible')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habilidad');
    }
};
