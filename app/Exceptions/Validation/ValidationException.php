<?php

namespace App\Exceptions\Validation;

use Exception;

class ValidationException extends Exception
{
    protected $code = 422;
    protected array $errors;

    public function __construct(string $message, array $errors = [])
    {
        parent::__construct($message, $this->code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}