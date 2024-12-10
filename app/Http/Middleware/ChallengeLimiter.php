<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\Validation\RateLimitExceededException;

class ChallengeLimiter extends ThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @param  string  $prefix
     * @return Response
     *
     * @throws RateLimitExceededException
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = ''): Response
    {
        // Crear una clave única para este límite
        $key = $prefix.$this->resolveRequestSignature($request);

        // Verificar si hay demasiados intentos
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $secondsUntilAvailable = RateLimiter::availableIn($key);
            $attemptsLeft = 0;
            
            return response()->json([
                'status' => 'error',
                'message' => sprintf(
                    'Has excedido el límite de intentos. Por favor, espera %d minutos antes de intentarlo nuevamente.',
                    ceil($secondsUntilAvailable / 60)
                ),
                'limits' => [
                    'max_attempts' => $maxAttempts,
                    'attempts_left' => $attemptsLeft,
                    'retry_after_seconds' => $secondsUntilAvailable,
                    'retry_after_minutes' => ceil($secondsUntilAvailable / 60)
                ]
            ], 429)->withHeaders([
                'Retry-After' => $secondsUntilAvailable,
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        // Incrementar el contador de intentos
        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Calcular intentos restantes
        $attemptsLeft = RateLimiter::remaining($key, $maxAttempts);

        // Añadir headers de rate limit
        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $attemptsLeft,
        ]);
    }
}