<?php

namespace App\Exceptions\Auth;

use Exception;

class InvalidTokenException extends Exception
{
    protected $code = 401;

    public function __construct()
    {
        parent::__construct('Token inválido o malformado.');
    }
}