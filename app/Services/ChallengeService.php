<?php

namespace App\Services;

use App\Exceptions\Challenge\ChallengeNotFoundException;
use App\Exceptions\Challenge\InvalidDumpTypeException;
use App\Exceptions\Challenge\DumpNotFoundException;
use App\Repositories\Interfaces\ChallengeRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ChallengeService
{
    public const VALID_DUMP_TYPES = ['json', 'sql'];
    private const CACHE_TTL = 300; // 5 minutos

    public function __construct(
        private ChallengeRepositoryInterface $challengeRepository
    ) {}

    /**
     * Get current challenge with caching
     *
     * @throws ChallengeNotFoundException
     * @return array
     */
    public function getCurrentChallenge(): array
    {
        return Cache::remember('current_challenge', self::CACHE_TTL, function () {
            $challenge = $this->challengeRepository->getChallenge();

            if (empty($challenge)) {
                throw new ChallengeNotFoundException('No hay desafíos disponibles en este momento.');
            }

            if (!isset($challenge['challenge_id'], $challenge['description'])) {
                throw new ChallengeNotFoundException('El desafío está mal formado o incompleto.');
            }

            return $challenge;
        });
    }

    /**
     * Get dump data by type with validation and caching
     *
     * @param string $dumpType
     * @throws InvalidDumpTypeException
     * @throws DumpNotFoundException
     * @return array
     */
    public function getDumpData(string $dumpType): array
    {
        if (!in_array($dumpType, self::VALID_DUMP_TYPES, true)) {
            throw new InvalidDumpTypeException(
                sprintf(
                    'El tipo de dump "%s" no es válido. Tipos válidos: %s',
                    $dumpType,
                    implode(', ', self::VALID_DUMP_TYPES)
                )
            );
        }

        return Cache::remember("dump_data_{$dumpType}", self::CACHE_TTL, function () use ($dumpType) {
            $dumps = $this->challengeRepository->getDumpsByType($dumpType);

            if (empty($dumps)) {
                throw new DumpNotFoundException(
                    sprintf('No se encontraron datos para el dump tipo "%s".', $dumpType)
                );
            }

            if (!isset($dumps['groups']) || !is_array($dumps['groups'])) {
                throw new DumpNotFoundException(
                    sprintf('Los datos del dump tipo "%s" están mal formados o incompletos.', $dumpType)
                );
            }

            return $dumps;
        });
    }
}