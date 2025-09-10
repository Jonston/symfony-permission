<?php

namespace Jonston\SymfonyPermission\Tests\Entity;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;
use PHPUnit\Framework\TestCase;

class PermissionTest extends TestCase
{
    public function testDescription(): void
    {
        $permission = new Permission();
        $this->assertNull($permission->getDescription());
        $permission->setDescription('desc');
        $this->assertEquals('desc', $permission->getDescription());
    }

    public function testToString(): void
    {
        $permission = new Permission();
        $permission->setName('edit');
        $this->assertEquals('edit', (string)$permission);
    }

    public function testRolesRelation(): void
    {
        $permission = new Permission();
        $role = new Role();
        $role->addPermission($permission);
        $this->assertTrue($role->hasPermission($permission));
    }
}

