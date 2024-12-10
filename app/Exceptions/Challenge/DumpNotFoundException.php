<?php

namespace App\Exceptions\Challenge;

use Exception;

class DumpNotFoundException extends Exception
{
    protected $code = 404;

    public function __construct(string $message)
    {
        parent::__construct($message, $this->code);
    }
}