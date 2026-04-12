<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyecto', function (Blueprint $table) {
$table->string('id_proyecto')->primary();
            $table->string('id_portafolio'); // Referencia al dueño (Portafolio)
            
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('url_proyecto')->nullable(); // Link al repo o demo
            $table->string('imagen_url')->nullable();   // Miniatura del proyecto
            
            $table->date('fecha_ini')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->timestamp('creado_en')->nullable();

            // Llave foránea: Si se borra el portafolio, se borran sus proyectos
            $table->foreign('id_portafolio')
            ->references('id_portafolio')
            ->on('portafolio')
            ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto');
    }
};