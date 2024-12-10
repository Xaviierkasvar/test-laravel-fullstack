<?php

namespace Tests\Unit;

use App\Repositories\ChallengeRepository;
use Tests\TestCase;

class ChallengeRepositoryTest extends TestCase
{
    private ChallengeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ChallengeRepository();
    }

    public function test_get_dumps_by_type_returns_expected_data()
    {
        $result = $this->repository->getDumpsByType('json');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('groups', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertCount(2, $result['groups']);
        $this->assertEquals('Group A', $result['groups'][0]['name']);
    }

    public function test_get_challenge_returns_expected_data()
    {
        $result = $this->repository->getChallenge();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('challenge_id', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('hint', $result);

        $this->assertEquals(1, $result['challenge_id']);
        $this->assertEquals('Analyze the group structure in the dump', $result['description']);
    }
}
