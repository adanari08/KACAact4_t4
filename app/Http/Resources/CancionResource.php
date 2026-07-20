<?php

// API Resource para Cancion. Da forma al JSON de salida,
// incluyendo el álbum (relación 1:N) y los géneros (relación N:M)
// ya "aplanados" en un formato limpio, en vez de mandar toda la
// estructura interna de Eloquent.

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CancionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'duracion_segundos' => $this->duracion_segundos,
            'duracion_formateada' => $this->duracion_formateada,
            'anio' => $this->anio,
            'album' => [
                'id' => $this->album->id,
                'titulo' => $this->album->titulo,
                'artista' => $this->album->artista,
            ],
            'generos' => $this->generos->map(fn ($genero) => [
                'id' => $genero->id,
                'nombre' => $genero->nombre,
            ]),
        ];
    }
}
