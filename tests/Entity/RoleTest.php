<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Tests\Entity;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testRoleCreation(): void
    {
        $role = new Role();
        $role->setName('admin');
        $role->setGuardName('web');

        $this->assertEquals('admin', $role->getName());
        $this->assertEquals('web', $role->getGuardName());
        $this->assertInstanceOf(\DateTimeImmutable::class, $role->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $role->getUpdatedAt());
    }

    public function testRoleToString(): void
    {
        $role = new Role();
        $role->setName('admin');

        $this->assertEquals('admin', (string) $role);
    }

    public function testPermissionRelationship(): void
    {
        $role = new Role();
        $role->setName('admin');

        $permission = new Permission();
        $permission->setName('edit-posts');

        $role->addPermission($permission);

        $this->assertTrue($role->getPermissions()->contains($permission));
        $this->assertTrue($permission->getRoles()->contains($role));
    }

    public function testHasPermission(): void
    {
        $role = new Role();
        $role->setName('admin');

        $permission = new Permission();
        $permission->setName('edit-posts');

        $role->addPermission($permission);

        $this->assertTrue($role->hasPermission('edit-posts'));
        $this->assertFalse($role->hasPermission('delete-posts'));
    }

    public function testRemovePermission(): void
    {
        $role = new Role();
        $role->setName('admin');

        $permission = new Permission();
        $permission->setName('edit-posts');

        $role->addPermission($permission);
        $role->removePermission($permission);

        $this->assertFalse($role->getPermissions()->contains($permission));
        $this->assertFalse($role->hasPermission('edit-posts'));
    }
}
