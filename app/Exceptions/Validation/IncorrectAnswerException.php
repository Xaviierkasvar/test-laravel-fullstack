<?php

namespace App\Exceptions\Validation;

class IncorrectAnswerException extends ValidationException
{
    public function __construct()
    {
        parent::__construct(
            'La respuesta es incorrecta. Por favor, revisa los nombres de los grupos.',
            ['answer' => 'La respuesta proporcionada no coincide con la esperada.']
        );
    }
}