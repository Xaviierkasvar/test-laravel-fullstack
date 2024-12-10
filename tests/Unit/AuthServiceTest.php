<?php

namespace Tests\Unit;

use App\Services\AuthService;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\TokenExpiredException;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    public function test_login_with_valid_credentials()
    {
        $authService = new AuthService();

        $credentials = [
            'username' => 'queo_challenge',
            'password' => 'queoChallenge',
        ];

        $response = $authService->login($credentials);

        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('expires_in', $response);
        $this->assertEquals('Authentication successful', $response['message']);
    }

    public function test_login_with_invalid_credentials()
    {
        $this->expectException(InvalidCredentialsException::class);

        $authService = new AuthService();

        $credentials = [
            'username' => 'invalid_user',
            'password' => 'wrong_password',
        ];

        $authService->login($credentials);
    }

    public function test_validate_token_success()
    {
        $authService = new AuthService();
        $credentials = [
            'username' => 'queo_challenge',
            'password' => 'queoChallenge',
        ];

        $response = $authService->login($credentials);

        $isValid = $authService->validateToken($response['token']);

        $this->assertTrue($isValid);
    }

    public function test_validate_token_expired()
    {
        $this->expectException(TokenExpiredException::class);

        $authService = new AuthService();
        
        // Generar token manualmente con timestamp pasado
        $expiredToken = '00000000.' . bin2hex(random_bytes(32));

        $authService->validateToken($expiredToken);
    }

    public function test_get_token_expiration()
    {
        $authService = new AuthService();
        $credentials = [
            'username' => 'queo_challenge',
            'password' => 'queoChallenge',
        ];

        $response = $authService->login($credentials);
        $expirationTimestamp = $authService->getTokenExpiration($response['token']);

        $this->assertNotNull($expirationTimestamp);
        $this->assertGreaterThan(time(), $expirationTimestamp);
    }
}
