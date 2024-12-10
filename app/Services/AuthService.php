<?php

namespace App\Services;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\TokenExpiredException;

class AuthService
{
    private const TOKEN_EXPIRATION_MINUTES = 15;

    public function login(array $credentials): array
    {
        if (
            $credentials['username'] === 'queo_challenge' &&
            $credentials['password'] === 'queoChallenge'
        ) {
            return [
                'token' => $this->generateToken(),
                'message' => 'Authentication successful',
                'expires_in' => self::TOKEN_EXPIRATION_MINUTES * 60 // en segundos
            ];
        }

        throw new InvalidCredentialsException();
    }

    /**
     * Generate a token with expiration timestamp
     */
    private function generateToken(): string
    {
        $timestamp = time() + (self::TOKEN_EXPIRATION_MINUTES * 60);
        $randomBytes = random_bytes(32);
        
        // Combinar timestamp y bytes aleatorios
        $tokenParts = [
            bin2hex(pack('N', $timestamp)), // 8 caracteres para el timestamp
            bin2hex($randomBytes)           // 64 caracteres aleatorios
        ];
        
        return implode('.', $tokenParts);
    }

    /**
     * Validate token and its expiration
     *
     * @throws TokenExpiredException
     * @return bool
     */
    public function validateToken(string $token): bool
    {
        $parts = explode('.', $token);
        
        if (count($parts) !== 2) {
            return false;
        }

        [$timestampHex, $tokenPart] = $parts;

        // Validar formato
        if (!preg_match('/^[a-f0-9]{8}$/i', $timestampHex) || 
            !preg_match('/^[a-f0-9]{64}$/i', $tokenPart)) {
            return false;
        }

        // Extraer timestamp
        $expirationTime = unpack('N', hex2bin($timestampHex))[1];
        
        // Verificar expiración
        if (time() > $expirationTime) {
            throw new TokenExpiredException();
        }

        return true;
    }

    /**
     * Get token expiration timestamp
     */
    public function getTokenExpiration(string $token): ?int
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return null;
        }

        $timestampHex = $parts[0];
        if (!preg_match('/^[a-f0-9]{8}$/i', $timestampHex)) {
            return null;
        }

        return unpack('N', hex2bin($timestampHex))[1];
    }
}