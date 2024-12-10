<?php

namespace App\Http\Controllers;

use App\Services\ValidateService;
use App\DTOs\ValidationRequestDTO;
use App\Exceptions\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Validation",
 *     description="API Endpoints para validación de respuestas al desafío"
 * )
 */
class ValidateController extends Controller
{
    public function __construct(
        private ValidateService $validateService
    ) {
        $this->middleware('custom.auth');
        $this->middleware('challenge.limit:1,60');
    }

    /**
     * @OA\Post(
     *     path="/api/validate",
     *     summary="Validar respuesta al desafío",
     *     description="Valida la respuesta proporcionada para el desafío actual. Limitado a 1 intento por hora.",
     *     operationId="validateChallenge",
     *     tags={"Validation"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"number_of_groups", "answer"},
     *             @OA\Property(
     *                 property="number_of_groups",
     *                 type="integer",
     *                 example=2,
     *                 description="Número total de grupos encontrados"
     *             ),
     *             @OA\Property(
     *                 property="answer",
     *                 type="string",
     *                 example="Group A, Group B",
     *                 description="Nombres de los grupos separados por coma"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Respuesta validada correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", example="correct"),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="¡Tu respuesta es correcta!"
     *                 ),
     *                 @OA\Property(
     *                     property="timestamp",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-12-10T06:31:42Z"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Token inválido o expirado."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validación fallida",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="La respuesta es incorrecta. Por favor, revisa los nombres de los grupos."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="number_of_groups",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="El número de grupos no coincide con la solución."
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Demasiadas solicitudes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Has excedido el límite de intentos. Por favor, espera 60 segundos."
     *             ),
     *             @OA\Property(
     *                 property="limits",
     *                 type="object",
     *                 @OA\Property(property="max_attempts", type="integer", example=1),
     *                 @OA\Property(property="attempts_left", type="integer", example=0),
     *                 @OA\Property(property="retry_after_seconds", type="integer", example=60),
     *                 @OA\Property(property="retry_after_minutes", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Ha ocurrido un error inesperado durante la validación."
     *             ),
     *             @OA\Property(
     *                 property="reference_id",
     *                 type="string",
     *                 example="err_5f3e9b2c1d"
     *             )
     *         )
     *     )
     * )
     */
    public function validateChallenge(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'number_of_groups' => 'required|integer|min:1',
                'answer' => 'required|string|min:1'
            ]);

            $validationDTO = new ValidationRequestDTO(
                $validated['number_of_groups'],
                $validated['answer']
            );

            $result = $this->validateService->validateAnswer($validationDTO);

            return response()->json([
                'status' => 'success',
                'data' => $result
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], $e->getCode());
        } catch (\Exception $e) {
            Log::error('Validation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Ha ocurrido un error inesperado durante la validación.',
                'reference_id' => uniqid('err_')
            ], 500);
        }
    }
}