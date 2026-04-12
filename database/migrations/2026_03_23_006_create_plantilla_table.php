<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantilla', function (Blueprint $table) {
            $table->string('id_plantilla')->primary(); //ULID
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('url_vista')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla');
    }
};