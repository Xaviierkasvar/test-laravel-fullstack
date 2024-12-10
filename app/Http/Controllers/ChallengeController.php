<?php

namespace App\Http\Controllers;

use App\Services\ChallengeService;
use App\Exceptions\Challenge\ChallengeNotFoundException;
use App\Exceptions\Challenge\InvalidDumpTypeException;
use App\Exceptions\Challenge\DumpNotFoundException;
use App\Exceptions\Auth\TokenExpiredException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Challenge",
 *     description="API Endpoints para gestión de desafíos"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class ChallengeController extends Controller
{
    public function __construct(
        private ChallengeService $challengeService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/challenge",
     *     summary="Obtener el desafío actual",
     *     description="Retorna la información del desafío actual del sistema",
     *     operationId="getChallenge",
     *     tags={"Challenge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Desafío obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="challenge_id", type="integer", example=1),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Analyze the group structure in the dump"
     *                 ),
     *                 @OA\Property(
     *                     property="hint",
     *                     type="string",
     *                     example="Use the dumps endpoint to get the necessary data"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="timestamp",
     *                 type="string",
     *                 format="date-time",
     *                 example="2024-12-10T06:31:42Z"
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
     *                 example="No se encontró el token de autorización."
     *             ),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Desafío no encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No hay desafíos disponibles en este momento."
     *             ),
     *             @OA\Property(property="code", type="integer", example=404)
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
     *                 example="Ha ocurrido un error inesperado al obtener el desafío."
     *             ),
     *             @OA\Property(property="code", type="integer", example=500),
     *             @OA\Property(
     *                 property="reference_id",
     *                 type="string",
     *                 example="err_5f3e9b2c1d"
     *             )
     *         )
     *     )
     * )
     */
    public function getChallenge(): JsonResponse
    {
        try {
            $challenge = $this->challengeService->getCurrentChallenge();
            
            return response()->json([
                'status' => 'success',
                'data' => $challenge,
                'timestamp' => now()->toIso8601String()
            ]);

        } catch (ChallengeNotFoundException $e) {
            Log::warning('Challenge not found', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], $e->getCode());
        } catch (\Exception $e) {
            Log::error('Unexpected error in getChallenge', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Ha ocurrido un error inesperado al obtener el desafío.',
                'code' => 500,
                'reference_id' => uniqid('err_')
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/dumps/{dump_type}",
     *     summary="Obtener dumps por tipo",
     *     description="Retorna los datos del dump según el tipo especificado",
     *     operationId="getDumps",
     *     tags={"Challenge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="dump_type",
     *         in="path",
     *         required=true,
     *         description="Tipo de dump (json o sql)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"json", "sql"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dumps obtenidos exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="groups",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Group A")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 @OA\Property(property="type", type="string", example="json"),
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
     *         response=400,
     *         description="Tipo de dump inválido",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="El tipo de dump 'xml' no es válido. Tipos válidos: json, sql"
     *             ),
     *             @OA\Property(
     *                 property="valid_types",
     *                 type="array",
     *                 @OA\Items(type="string", example={"json", "sql"})
     *             ),
     *             @OA\Property(property="code", type="integer", example=400)
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
     *             ),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dumps no encontrados",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No se encontraron datos para el dump tipo 'json'."
     *             ),
     *             @OA\Property(property="code", type="integer", example=404)
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
     *                 example="Ha ocurrido un error inesperado al obtener los dumps."
     *             ),
     *             @OA\Property(property="code", type="integer", example=500),
     *             @OA\Property(
     *                 property="reference_id",
     *                 type="string",
     *                 example="err_5f3e9b2c1d"
     *             )
     *         )
     *     )
     * )
     */
    public function getDumps(string $dump_type): JsonResponse
    {
        try {
            // Sanitizar el tipo de dump
            $dump_type = strtolower(trim($dump_type));
            
            $dumps = $this->challengeService->getDumpData($dump_type);
            
            return response()->json([
                'status' => 'success',
                'data' => $dumps,
                'metadata' => [
                    'type' => $dump_type,
                    'timestamp' => now()->toIso8601String()
                ]
            ]);

        } catch (InvalidDumpTypeException $e) {
            Log::warning('Invalid dump type requested', [
                'type' => $dump_type,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'valid_types' => ChallengeService::VALID_DUMP_TYPES,
                'code' => $e->getCode()
            ], $e->getCode());
        } catch (DumpNotFoundException $e) {
            Log::warning('Dump not found', [
                'type' => $dump_type,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], $e->getCode());
        } catch (\Exception $e) {
            Log::error('Unexpected error in getDumps', [
                'type' => $dump_type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Ha ocurrido un error inesperado al obtener los dumps.',
                'code' => 500,
                'reference_id' => uniqid('err_')
            ], 500);
        }
    }
}