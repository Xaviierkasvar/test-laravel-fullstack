<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\Auth\TokenExpiredException;
use App\Exceptions\Auth\InvalidTokenException;
use App\Exceptions\Auth\TokenNotFoundException;
use App\Exceptions\Auth\InvalidCredentialsException;
use Throwable;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenExpiredException ||
            $exception instanceof InvalidTokenException ||
            $exception instanceof TokenNotFoundException ||
            $exception instanceof InvalidCredentialsException) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ], $exception->getCode());
        }

        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'El token ha expirado. Por favor, inicie sesión nuevamente.',
                'code' => 401
            ], 401);
        }

        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Token inválido.',
                'code' => 401
            ], 401);
        }

        return parent::render($request, $exception);
    }
}