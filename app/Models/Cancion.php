<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cancion extends Model
{
    use HasFactory;

    // Igual que en Act3: Eloquent pluraliza mal "Cancion" en inglés
    // ("cancions"), así que fijamos el nombre real de la tabla.
    protected $table = 'canciones';

    protected $fillable = ['titulo', 'duracion_segundos', 'anio', 'album_id'];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function generos(): BelongsToMany
    {
        return $this->belongsToMany(Genero::class);
    }

    public function getDuracionFormateadaAttribute(): string
    {
        $minutos = intdiv($this->duracion_segundos, 60);
        $segundos = $this->duracion_segundos % 60;
        return sprintf('%d:%02d', $minutos, $segundos);
    }
}
