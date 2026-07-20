<?php

// Seeder simple: solo crea unos cuantos álbumes y géneros
// para tener con qué probar el CRUD de canciones desde Bruno.
// Las canciones NO se crean aquí a propósito: esas las vamos a
// crear nosotros mismos probando el endpoint POST /api/canciones,
// que es justo una de las cosas que pide la actividad.
//
// Se ejecuta con:
//   php artisan migrate:fresh --seed
// o, si ya migraste antes:
//   php artisan db:seed

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Genero;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Géneros musicales básicos
        $nombresGeneros = ['Pop', 'Rock', 'Electrónica', 'Hip-Hop', 'Jazz', 'Reggaetón', 'Balada'];
        foreach ($nombresGeneros as $nombre) {
            Genero::firstOrCreate(['nombre' => $nombre]);
        }

        // Un par de álbumes de prueba para poder asignarles canciones
        Album::firstOrCreate(
            ['titulo' => 'Horizonte eterno'],
            ['artista' => 'Sombra Eléctrica', 'anio_lanzamiento' => 2019]
        );

        Album::firstOrCreate(
            ['titulo' => 'Luces de Neón'],
            ['artista' => 'Valeria Cruz', 'anio_lanzamiento' => 2021]
        );

        Album::firstOrCreate(
            ['titulo' => 'Ecos del Mar'],
            ['artista' => 'Nala Ríos', 'anio_lanzamiento' => 2020]
        );
    }
}