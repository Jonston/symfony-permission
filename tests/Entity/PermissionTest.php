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
        $permission = new Permission('edit-posts', 'Permission to edit posts');

        $this->assertNull($permission->getId());
        $this->assertEquals('edit-posts', $permission->getName());
        $this->assertEquals('Permission to edit posts', $permission->getDescription());
        $this->assertInstanceOf(\DateTimeImmutable::class, $permission->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $permission->getUpdatedAt());
        $this->assertCount(0, $permission->getRoles());
    }

    public function testPermissionCreationWithoutDescription(): void
    {
        $permission = new Permission('delete-posts');

        $this->assertEquals('delete-posts', $permission->getName());
        $this->assertNull($permission->getDescription());
    }

    public function testSetName(): void
    {
        $permission = new Permission('old-name');
        $originalUpdatedAt = $permission->getUpdatedAt();

        // Wait a moment to ensure different timestamps
        usleep(1000);

        $permission->setName('new-name');

        $this->assertEquals('new-name', $permission->getName());
        $this->assertGreaterThan($originalUpdatedAt, $permission->getUpdatedAt());
    }

    public function testSetDescription(): void
    {
        $permission = new Permission('test-permission');
        $originalUpdatedAt = $permission->getUpdatedAt();

        usleep(1000);

        $permission->setDescription('New description');

        $this->assertEquals('New description', $permission->getDescription());
        $this->assertGreaterThan($originalUpdatedAt, $permission->getUpdatedAt());
    }

    public function testRoleRelationship(): void
    {
        $permission = new Permission('test-permission');
        $role = new Role('test-role');

        $role->addPermission($permission);

        $this->assertTrue($permission->getRoles()->contains($role));
    }
}
