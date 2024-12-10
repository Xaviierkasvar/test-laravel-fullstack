<?php

namespace App\Exceptions\Validation;

class IncorrectGroupCountException extends ValidationException
{
    public function __construct(int $providedCount, int $actualCount)
    {
        parent::__construct(
            "El número de grupos proporcionado ({$providedCount}) no coincide con el número real ({$actualCount}).",
            ['number_of_groups' => "Se esperaban {$actualCount} grupos."]
        );
    }
}