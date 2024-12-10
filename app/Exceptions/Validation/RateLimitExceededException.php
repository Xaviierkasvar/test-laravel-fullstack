<?php

namespace App\Exceptions\Validation;

class RateLimitExceededException extends ValidationException
{
    protected $code = 429;
    private int $maxAttempts;
    private int $secondsUntilAvailable;
    private int $attemptsLeft;

    public function __construct(int $maxAttempts, int $secondsUntilAvailable, int $attemptsLeft)
    {
        $this->maxAttempts = $maxAttempts;
        $this->secondsUntilAvailable = $secondsUntilAvailable;
        $this->attemptsLeft = $attemptsLeft;

        $minutes = ceil($secondsUntilAvailable / 60);
        
        parent::__construct(
            sprintf(
                'Has excedido el límite de intentos. Por favor, espera %d minutos antes de intentarlo nuevamente.',
                $minutes
            ),
            [
                'rate_limit' => [
                    'max_attempts' => $maxAttempts,
                    'attempts_left' => $attemptsLeft,
                    'seconds_until_available' => $secondsUntilAvailable,
                    'minutes_until_available' => $minutes
                ]
            ]
        );
    }

    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    public function getSecondsUntilAvailable(): int
    {
        return $this->secondsUntilAvailable;
    }

    public function getAttemptsLeft(): int
    {
        return $this->attemptsLeft;
    }
}