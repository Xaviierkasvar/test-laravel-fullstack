<?php

namespace App\Repositories\Interfaces;

interface ChallengeRepositoryInterface
{
    /**
     * Get the current challenge data
     *
     * @return array
     */
    public function getChallenge(): array;

    /**
     * Get dumps by type
     *
     * @param string $type
     * @return array
     */
    public function getDumpsByType(string $type): array;
}