<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Tests\Entity;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;
use PHPUnit\Framework\TestCase;

class PermissionTest extends TestCase
{
    public function testPermissionCreation(): void
    {
        $permission = new Permission();
        $permission->setName('edit-posts');
        $permission->setGuardName('web');

        $this->assertEquals('edit-posts', $permission->getName());
        $this->assertEquals('web', $permission->getGuardName());
        $this->assertInstanceOf(\DateTimeImmutable::class, $permission->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $permission->getUpdatedAt());
    }

    public function testPermissionToString(): void
    {
        $permission = new Permission();
        $permission->setName('edit-posts');

        $this->assertEquals('edit-posts', (string) $permission);
    }

    public function testRoleRelationship(): void
    {
        $permission = new Permission();
        $permission->setName('edit-posts');

        $role = new Role();
        $role->setName('editor');

        $permission->addRole($role);

        $this->assertTrue($permission->getRoles()->contains($role));
        $this->assertTrue($role->getPermissions()->contains($permission));
    }

    public function testPermissionRemoveRole(): void
    {
        $permission = new Permission();
        $permission->setName('edit-posts');

        $role = new Role();
        $role->setName('editor');

        $permission->addRole($role);
        $permission->removeRole($role);

        $this->assertFalse($permission->getRoles()->contains($role));
        $this->assertFalse($role->getPermissions()->contains($permission));
    }
}
