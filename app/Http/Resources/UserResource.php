<?php

// API Resource para el Usuario. Controla exactamente qué campos
// salen en el JSON cuando devolvemos un usuario (por ejemplo, en
// register/login). El campo "password" NUNCA se incluye aquí
// a propósito, aunque ya está oculto por defecto en el modelo
// User (protected $hidden), esta es una segunda capa explícita
// de control sobre qué exponemos.

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
