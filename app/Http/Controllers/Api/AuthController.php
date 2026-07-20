<?php

// Controlador de autenticación: registro, login y logout.
// Aquí es donde Sanctum entra en acción: al registrarse o
// iniciar sesión, se genera un token real que el cliente
// (Bruno, o después React) debe guardar y mandar en cada
// petición protegida.

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registra un usuario nuevo y le regresa un token de acceso.
     * Si la validación falla, Laravel responde automáticamente
     * con código 422 y un JSON describiendo los errores.
     */
    public function register(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
        ]);

        // createToken() es un método que existe gracias al trait
        // HasApiTokens del modelo User. Genera un token nuevo y
        // regresa el texto plano UNA sola vez (después ya no se
        // puede volver a ver, solo se guarda su hash en la BD).
        $token = $user->createToken('token-api')->plainTextToken;

        return response()->json([
            'usuario' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Inicia sesión: valida credenciales y regresa un token nuevo.
     */
    public function login(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $datos['email'])->first();

        if (! $user || ! Hash::check($datos['password'], $user->password)) {
            // ValidationException también produce automáticamente
            // una respuesta 422 en formato JSON.
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $token = $user->createToken('token-api')->plainTextToken;

        return response()->json([
            'usuario' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Cierra sesión: elimina SOLO el token que se usó en esta
     * petición (currentAccessToken), no todos los tokens del usuario.
     * Requiere ir protegida por el middleware auth:sanctum.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'mensaje' => 'Sesión cerrada correctamente.',
        ]);
    }

    /**
     * Devuelve los datos del usuario autenticado actualmente.
     * Útil para probar que el token funciona.
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
