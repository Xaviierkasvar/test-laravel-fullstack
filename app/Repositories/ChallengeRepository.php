<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ChallengeRepositoryInterface;

class ChallengeRepository implements ChallengeRepositoryInterface
{
    public function getDumpsByType(string $type): array
    {
        // Aquí implementarías la lógica para obtener los datos según el tipo
        // Por ejemplo, podrías tener diferentes conjuntos de datos para diferentes tipos
        return [
            'groups' => [
                ['id' => 1, 'name' => 'Group A'],
                ['id' => 2, 'name' => 'Group B'],
            ],
            'data' => [
                'example_key' => 'example_value',
            ]
        ];
    }

    public function getChallenge(): array
    {
        return [
            'challenge_id' => 1,
            'description' => 'Analyze the group structure in the dump',
            'hint' => 'Use the dumps endpoint to get the necessary data'
        ];
    }
}