<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tecnologia', function (Blueprint $table) {
            $table->string('id_tecnologia')->primary();
            $table->string('nombre');
            $table->string('categoria')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tecnologia');
    }
};