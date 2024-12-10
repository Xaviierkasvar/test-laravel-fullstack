<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Exceptions\Auth\InvalidCredentialsException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints para autenticación"
 * )
 */
class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Autenticar usuario",
     *     description="Endpoint para autenticar un usuario y obtener un token JWT",
     *     operationId="login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username","password"},
     *             @OA\Property(property="username", type="string", example="queo_challenge"),
     *             @OA\Property(property="password", type="string", example="queoChallenge")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login exitoso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", 
     *                     example="64a3b021.7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2c3d4e5f6a7b8"),
     *                 @OA\Property(property="message", type="string", example="Authentication successful"),
     *                 @OA\Property(property="expires_in", type="integer", example=900,
     *                     description="Tiempo de expiración en segundos (15 minutos)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos de entrada inválidos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Datos de entrada inválidos"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="username",
     *                     type="array",
     *                     @OA\Items(type="string", example="El campo username es obligatorio")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales inválidas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Credenciales inválidas"),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Ha ocurrido un error inesperado"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string'
            ]);

            $result = $this->authService->login($credentials);

            return response()->json([
                'status' => 'success',
                'data' => $result
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (InvalidCredentialsException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], $e->getCode());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ha ocurrido un error inesperado',
                'code' => 500
            ], 500);
        }
    }
}