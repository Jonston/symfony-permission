<?php

namespace Jonston\SymfonyPermission\Tests\Dto;

use Jonston\SymfonyPermission\Dto\Permission\CreatePermissionDto;
use Jonston\SymfonyPermission\Dto\Role\CreateRoleDto;
use PHPUnit\Framework\TestCase;

class AbstractDtoTest extends TestCase
{
    public function testToArrayAndFromArrayPermission(): void
    {
        $dto = new CreatePermissionDto('edit', 'desc');
        $array = $dto->toArray();
        $this->assertEquals(['name' => 'edit', 'description' => 'desc'], $array);
        $dto2 = CreatePermissionDto::fromArray(['name' => 'edit', 'description' => 'desc']);
        $this->assertEquals($dto->name, $dto2->name);
        $this->assertEquals($dto->description, $dto2->description);
    }

    public function testToArrayAndFromArrayRole(): void
    {
        $dto = new CreateRoleDto('admin', 'desc');
        $array = $dto->toArray();
        $this->assertEquals(['name' => 'admin', 'description' => 'desc'], $array);
        $dto2 = CreateRoleDto::fromArray(['name' => 'admin', 'description' => 'desc']);
        $this->assertEquals($dto->name, $dto2->name);
        $this->assertEquals($dto->description, $dto2->description);
    }
}

