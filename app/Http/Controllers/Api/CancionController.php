<?php

// CRUD completo de Canciones, en formato API (JSON).
// Todas estas rutas están protegidas por el middleware
// auth:sanctum (definido en routes/api.php), así que solo se
// puede llegar aquí si la petición trae un token válido.

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CancionResource;
use App\Models\Cancion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CancionController extends Controller
{
    /**
     * GET /api/canciones — lista paginada.
     */
    public function index(): AnonymousResourceCollection
    {
        $canciones = Cancion::with(['album', 'generos'])
            ->orderBy('titulo')
            ->paginate(10);

        return CancionResource::collection($canciones);
    }

    /**
     * POST /api/canciones — crear una canción nueva.
     * Si algo no pasa la validación, Laravel responde 422
     * automáticamente con el detalle de los errores en JSON.
     */
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'titulo' => 'required|string|max:255',
            'duracion_segundos' => 'required|integer|min:1',
            'anio' => 'nullable|integer|min:1900|max:2100',
            'album_id' => 'required|exists:albums,id',
            'generos' => 'array',
            'generos.*' => 'exists:generos,id',
        ]);

        $cancion = Cancion::create($datos);
        $cancion->generos()->sync($datos['generos'] ?? []);

        return (new CancionResource($cancion->load('album', 'generos')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED); // 201
    }

    /**
     * GET /api/canciones/{cancion} — ver una canción individual.
     */
    public function show(Cancion $cancion): CancionResource
    {
        return new CancionResource($cancion->load('album', 'generos'));
    }

    /**
     * PUT/PATCH /api/canciones/{cancion} — actualizar.
     */
    public function update(Request $request, Cancion $cancion): CancionResource
    {
        $datos = $request->validate([
            'titulo' => 'required|string|max:255',
            'duracion_segundos' => 'required|integer|min:1',
            'anio' => 'nullable|integer|min:1900|max:2100',
            'album_id' => 'required|exists:albums,id',
            'generos' => 'array',
            'generos.*' => 'exists:generos,id',
        ]);

        $cancion->update($datos);
        $cancion->generos()->sync($datos['generos'] ?? []);

        return new CancionResource($cancion->load('album', 'generos'));
    }

    /**
     * DELETE /api/canciones/{cancion} — eliminar.
     * 204 = "No Content": la petición fue exitosa pero no hay
     * nada que devolver en el cuerpo de la respuesta.
     */
    public function destroy(Cancion $cancion): Response
    {
        $cancion->delete();

        return response()->noContent(); // 204
    }
}
