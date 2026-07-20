<?php

// Tabla pivote para la relación N:M entre canciones y generos.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cancion_genero', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cancion_id')->constrained('canciones')->cascadeOnDelete();
            $table->foreignId('genero_id')->constrained('generos')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['cancion_id', 'genero_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancion_genero');
    }
};
