<?php

namespace App\DTOs;

class ValidationRequestDTO
{
    public function __construct(
        public readonly int $numberOfGroups,
        public readonly string $answer
    ) {}
}