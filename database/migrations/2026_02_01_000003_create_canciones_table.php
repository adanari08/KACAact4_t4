<?php

// Tabla canciones (modelo principal de esta API), con album_id
// como llave foránea (relación 1:N).

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canciones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->unsignedInteger('duracion_segundos');
            $table->integer('anio')->nullable();
            $table->foreignId('album_id')
                  ->constrained('albums')
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canciones');
    }
};
