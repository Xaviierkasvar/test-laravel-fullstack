<?php

namespace App\Exceptions\Auth;

use Exception;

class TokenExpiredException extends Exception
{
    protected $code = 401;

    public function __construct()
    {
        parent::__construct('El token ha expirado. Por favor, inicie sesión nuevamente.');
    }
}