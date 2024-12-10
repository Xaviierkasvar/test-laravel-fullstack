<?php

namespace App\Http\Middleware;

use App\Exceptions\Auth\TokenExpiredException;
use App\Exceptions\Auth\InvalidTokenException;
use App\Exceptions\Auth\TokenNotFoundException;
use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomAuthentication
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                throw new TokenNotFoundException();
            }

            // Validará el token y lanzará TokenExpiredException si ha expirado
            if (!$this->authService->validateToken($token)) {
                throw new InvalidTokenException();
            }

            return $next($request);

        } catch (TokenNotFoundException $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        } catch (TokenExpiredException $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => 401
            ], 401);
        } catch (InvalidTokenException $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Error de autenticación.',
                'code' => 500
            ], 500);
        }
    }
}