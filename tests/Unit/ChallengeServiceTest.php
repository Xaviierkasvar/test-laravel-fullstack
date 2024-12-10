<?php

namespace Tests\Unit;

use App\Exceptions\Challenge\ChallengeNotFoundException;
use App\Exceptions\Challenge\InvalidDumpTypeException;
use App\Exceptions\Challenge\DumpNotFoundException;
use App\Services\ChallengeService;
use App\Repositories\Interfaces\ChallengeRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class ChallengeServiceTest extends TestCase
{
    private ChallengeRepositoryInterface $repositoryMock;
    private ChallengeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un mock del repositorio
        $this->repositoryMock = Mockery::mock(ChallengeRepositoryInterface::class);
        $this->service = new ChallengeService($this->repositoryMock);

        // Limpiar la caché
        Cache::flush();
    }

    public function test_get_current_challenge_success()
    {
        $challengeData = [
            'challenge_id' => 1,
            'description' => 'Analyze the group structure',
        ];

        $this->repositoryMock->shouldReceive('getChallenge')
            ->once()
            ->andReturn($challengeData);

        $result = $this->service->getCurrentChallenge();

        $this->assertEquals($challengeData, $result);
    }

    public function test_get_current_challenge_not_found()
    {
        $this->repositoryMock->shouldReceive('getChallenge')
            ->once()
            ->andReturn([]);

        $this->expectException(ChallengeNotFoundException::class);

        $this->service->getCurrentChallenge();
    }

    public function test_get_current_challenge_malformed()
    {
        $malformedChallenge = [
            'challenge_id' => 1,
        ];

        $this->repositoryMock->shouldReceive('getChallenge')
            ->once()
            ->andReturn($malformedChallenge);

        $this->expectException(ChallengeNotFoundException::class);

        $this->service->getCurrentChallenge();
    }

    public function test_get_dump_data_success()
    {
        $dumpType = 'json';
        $dumpData = [
            'groups' => [['id' => 1, 'name' => 'Group A']],
        ];

        $this->repositoryMock->shouldReceive('getDumpsByType')
            ->with($dumpType)
            ->once()
            ->andReturn($dumpData);

        $result = $this->service->getDumpData($dumpType);

        $this->assertEquals($dumpData, $result);
    }

    public function test_get_dump_data_invalid_type()
    {
        $this->expectException(InvalidDumpTypeException::class);

        $this->service->getDumpData('invalid_type');
    }

    public function test_get_dump_data_not_found()
    {
        $dumpType = 'json';

        $this->repositoryMock->shouldReceive('getDumpsByType')
            ->with($dumpType)
            ->once()
            ->andReturn([]);

        $this->expectException(DumpNotFoundException::class);

        $this->service->getDumpData($dumpType);
    }

    public function test_get_dump_data_malformed()
    {
        $dumpType = 'json';
        $malformedData = [
            'data' => ['key' => 'value'],
        ];

        $this->repositoryMock->shouldReceive('getDumpsByType')
            ->with($dumpType)
            ->once()
            ->andReturn($malformedData);

        $this->expectException(DumpNotFoundException::class);

        $this->service->getDumpData($dumpType);
    }
}
