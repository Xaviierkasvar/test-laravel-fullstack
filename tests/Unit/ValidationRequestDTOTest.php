<?php

namespace Tests\Unit;

use App\DTOs\ValidationRequestDTO;
use Tests\TestCase;

class ValidationRequestDTOTest extends TestCase
{
    public function test_dto_initialization()
    {
        $dto = new ValidationRequestDTO(
            numberOfGroups: 3,
            answer: 'Group A, Group B, Group C'
        );

        $this->assertSame(3, $dto->numberOfGroups);
        $this->assertSame('Group A, Group B, Group C', $dto->answer);
    }
}
