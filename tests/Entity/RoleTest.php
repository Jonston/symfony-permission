<?php

namespace Jonston\SymfonyPermission\Tests\Entity;

use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Entity\Permission;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testDescription(): void
    {
        $role = new Role();
        $this->assertNull($role->getDescription());
        $role->setDescription('desc');
        $this->assertEquals('desc', $role->getDescription());
    }

    public function testToString(): void
    {
        $role = new Role();
        $role->setName('admin');
        $this->assertEquals('admin', (string)$role);
    }

    public function testPermissionsRelation(): void
    {
        $role = new Role();
        $permission = new Permission();
        $role->addPermission($permission);
        $this->assertTrue($role->hasPermission($permission));
        $this->assertTrue($role->getPermissions()->contains($permission));
    }
}

