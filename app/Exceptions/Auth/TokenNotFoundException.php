<?php

namespace App\Exceptions\Auth;

use Exception;

class TokenNotFoundException extends Exception
{
    protected $code = 401;

    public function __construct()
    {
        parent::__construct('No se encontró el token de autorización.');
    }
}
