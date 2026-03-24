<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enlace_proyecto', function (Blueprint $table) {
            $table->string('id_enlace')->primary();
            $table->string('etiqueta');
            $table->string('url');
            $table->string('id_proyecto');

            $table->foreign('id_proyecto')
                ->references('id_proyecto')
                ->on('proyecto')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enlace_proyecto');
    }
};