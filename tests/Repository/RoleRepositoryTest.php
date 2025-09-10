<?php

namespace Jonston\SymfonyPermission\Tests\Repository;

use Jonston\SymfonyPermission\Repository\RoleRepository;
use PHPUnit\Framework\TestCase;

class RoleRepositoryTest extends TestCase
{
    public function testFindAllOrderedByNameExists(): void
    {
        $this->assertTrue(method_exists(RoleRepository::class, 'findAllOrderedByName'));
    }

    public function testFindOneByExists(): void
    {
        $this->assertTrue(method_exists(RoleRepository::class, 'findOneBy'));
    }
}

