<?php

namespace App\Exceptions\Challenge;

use Exception;

class ChallengeNotFoundException extends Exception
{
    protected $code = 404;

    public function __construct(string $message = 'No se encontró el desafío solicitado.')
    {
        parent::__construct($message, $this->code);
    }
}