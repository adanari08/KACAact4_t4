<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genero extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function canciones(): BelongsToMany
    {
        return $this->belongsToMany(Cancion::class);
    }
}
