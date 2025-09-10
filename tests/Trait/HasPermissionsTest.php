<?php

namespace Jonston\SymfonyPermission\Tests\Trait;

use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Entity\Permission;
use PHPUnit\Framework\TestCase;

class HasPermissionsTest extends TestCase
{
    public function testAddRemoveHasPermission(): void
    {
        $role = new Role();
        $permission = new Permission();
        $role->addPermission($permission);
        $this->assertTrue($role->hasPermission($permission));
        $role->removePermission($permission);
        $this->assertFalse($role->hasPermission($permission));
    }
}

