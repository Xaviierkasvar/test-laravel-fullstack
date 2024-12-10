<?php

namespace App\Exceptions\Challenge;

use Exception;

class InvalidDumpTypeException extends Exception
{
    protected $code = 400;

    public function __construct(string $message)
    {
        parent::__construct($message, $this->code);
    }
}