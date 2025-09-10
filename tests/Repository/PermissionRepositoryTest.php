<?php

namespace Jonston\SymfonyPermission\Tests\Repository;

use Jonston\SymfonyPermission\Repository\PermissionRepository;
use PHPUnit\Framework\TestCase;

class PermissionRepositoryTest extends TestCase
{
    public function testFindAllOrderedByNameExists(): void
    {
        $this->assertTrue(method_exists(PermissionRepository::class, 'findAllOrderedByName'));
    }

    public function testFindOneByExists(): void
    {
        $this->assertTrue(method_exists(PermissionRepository::class, 'findOneBy'));
    }
}

