<?php

namespace Tests\Unit;

use App\DTOs\ValidationRequestDTO;
use App\Exceptions\Validation\IncorrectGroupCountException;
use App\Exceptions\Validation\IncorrectAnswerException;
use App\Exceptions\Challenge\DumpNotFoundException;
use App\Services\ValidateService;
use App\Repositories\Interfaces\ChallengeRepositoryInterface;
use Mockery;
use Tests\TestCase;

class ValidateServiceTest extends TestCase
{
    private ChallengeRepositoryInterface $repositoryMock;
    private ValidateService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un mock del repositorio
        $this->repositoryMock = Mockery::mock(ChallengeRepositoryInterface::class);
        $this->service = new ValidateService($this->repositoryMock);
    }

    public function test_validate_answer_success()
    {
        $dumpData = [
            'groups' => [
                ['name' => 'Group A'],
                ['name' => 'Group B'],
            ],
        ];

        $this->repositoryMock->shouldReceive('getDumpsByType')
            ->with('default')
            ->once()
            ->andReturn($dumpData);

        $dto = new ValidationRequestDTO(
            numberOfGroups: 2,
            answer: 'Group A, Group B'
        );

        $result = $this->service->validateAnswer($dto);

        $this->assertEquals([
            'status' => 'correct',
            'message' => '¡Tu respuesta es correcta!',
            'timestamp' => now()->toIso8601String(),
        ], $result);
    }

    public function test_validate_answer_incorrect_group_count()
    {
        $dumpData = [
            'groups' => [
                ['name' => 'Group A'],
            ],
        ];

        $this->repositoryMock->shouldReceive('getDumpsByType')
            ->with('default')
            ->once()
            ->andReturn($dumpData);

        $dto = new ValidationRequestDTO(
            numberOfGroups: 2,
            answer: 'Group A'
        );

        $this->expectException(IncorrectGroupCountException::class);

        $this->service->validateAnswer($dto);
    }

    public function test_validate_answer_incorrect_answer()
    {
        $dumpData = [
            'groups' => [
                ['name' => 'Group A'],
                ['name' => 'Group B'],
            ],
        ];

        $this->repositoryMock->shouldReceive('getDumpsByType')
            ->with('default')
            ->once()
            ->andReturn($dumpData);

        $dto = new ValidationRequestDTO(
            numberOfGroups: 2,
            answer: 'Group A, Group C'
        );

        $this->expectException(IncorrectAnswerException::class);

        $this->service->validateAnswer($dto);
    }

    public function test_validate_answer_dump_not_found()
    {
        $this->repositoryMock->shouldReceive('getDumpsByType')
            ->with('default')
            ->once()
            ->andReturn([]);

        $dto = new ValidationRequestDTO(
            numberOfGroups: 1,
            answer: 'Group A'
        );

        $this->expectException(DumpNotFoundException::class);

        $this->service->validateAnswer($dto);
    }
}
