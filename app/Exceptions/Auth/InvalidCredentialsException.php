<?php

namespace App\Exceptions\Auth;

use Exception;

class InvalidCredentialsException extends Exception
{
    protected $code = 401;

    public function __construct()
    {
        parent::__construct('Credenciales inválidas. Por favor, verifique sus datos.');
    }
}