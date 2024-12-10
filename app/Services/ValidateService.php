<?php

namespace App\Services;

use App\DTOs\ValidationRequestDTO;
use App\Repositories\Interfaces\ChallengeRepositoryInterface;
use App\Exceptions\Validation\IncorrectGroupCountException;
use App\Exceptions\Validation\IncorrectAnswerException;
use App\Exceptions\Challenge\DumpNotFoundException;

class ValidateService
{
    public function __construct(
        private ChallengeRepositoryInterface $challengeRepository
    ) {}

    /**
     * Validate the challenge answer
     *
     * @param ValidationRequestDTO $dto
     * @throws IncorrectGroupCountException
     * @throws IncorrectAnswerException
     * @throws DumpNotFoundException
     * @return array
     */
    public function validateAnswer(ValidationRequestDTO $dto): array
    {
        $dump = $this->challengeRepository->getDumpsByType('default');

        if (!isset($dump['groups']) || !is_array($dump['groups'])) {
            throw new DumpNotFoundException('Los datos del dump son inválidos o están incompletos.');
        }

        $actualGroups = count($dump['groups']);
        if ($dto->numberOfGroups !== $actualGroups) {
            throw new IncorrectGroupCountException($dto->numberOfGroups, $actualGroups);
        }

        $expectedAnswer = implode(', ', array_column($dump['groups'], 'name'));
        if ($dto->answer !== $expectedAnswer) {
            throw new IncorrectAnswerException();
        }

        return [
            'status' => 'correct',
            'message' => '¡Tu respuesta es correcta!',
            'timestamp' => now()->toIso8601String()
        ];
    }
}