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
        $role = new Role('admin', 'Administrator role');

        $this->assertNull($role->getId());
        $this->assertEquals('admin', $role->getName());
        $this->assertEquals('Administrator role', $role->getDescription());
        $this->assertInstanceOf(\DateTimeImmutable::class, $role->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $role->getUpdatedAt());
        $this->assertCount(0, $role->getPermissions());
    }

    public function testRoleCreationWithoutDescription(): void
    {
        $role = new Role('user');

        $this->assertEquals('user', $role->getName());
        $this->assertNull($role->getDescription());
    }

    public function testSetName(): void
    {
        $role = new Role('old-name');
        $originalUpdatedAt = $role->getUpdatedAt();

        usleep(1000);

        $role->setName('new-name');

        $this->assertEquals('new-name', $role->getName());
        $this->assertGreaterThan($originalUpdatedAt, $role->getUpdatedAt());
    }

    public function testSetDescription(): void
    {
        $role = new Role('test-role');
        $originalUpdatedAt = $role->getUpdatedAt();

        usleep(1000);

        $role->setDescription('New description');

        $this->assertEquals('New description', $role->getDescription());
        $this->assertGreaterThan($originalUpdatedAt, $role->getUpdatedAt());
    }

    public function testAddPermission(): void
    {
        $role = new Role('test-role');
        $permission = new Permission('test-permission');
        $originalUpdatedAt = $role->getUpdatedAt();

        usleep(1000);

        $role->addPermission($permission);

        $this->assertTrue($role->getPermissions()->contains($permission));
        $this->assertGreaterThan($originalUpdatedAt, $role->getUpdatedAt());
    }

    public function testAddDuplicatePermission(): void
    {
        $role = new Role('test-role');
        $permission = new Permission('test-permission');

        $role->addPermission($permission);
        $role->addPermission($permission); // Add same permission again

        $this->assertCount(1, $role->getPermissions());
    }

    public function testRemovePermission(): void
    {
        $role = new Role('test-role');
        $permission = new Permission('test-permission');

        $role->addPermission($permission);
        $this->assertTrue($role->getPermissions()->contains($permission));

        usleep(1000);
        $originalUpdatedAt = $role->getUpdatedAt();
        usleep(1000);

        $role->removePermission($permission);

        $this->assertFalse($role->getPermissions()->contains($permission));
        $this->assertGreaterThan($originalUpdatedAt, $role->getUpdatedAt());
    }

    public function testRemoveNonExistentPermission(): void
    {
        $role = new Role('test-role');
        $permission = new Permission('test-permission');

        $role->removePermission($permission); // Remove permission that wasn't added

        $this->assertCount(0, $role->getPermissions());
    }

    public function testHasPermission(): void
    {
        $role = new Role('test-role');
        $permission = new Permission('test-permission');

        $this->assertFalse($role->hasPermission($permission));

        $role->addPermission($permission);

        $this->assertTrue($role->hasPermission($permission));
    }

    public function testHasPermissionByName(): void
    {
        $role = new Role('test-role');
        $permission = new Permission('edit-posts');

        $this->assertFalse($role->hasPermissionByName('edit-posts'));

        $role->addPermission($permission);

        $this->assertTrue($role->hasPermissionByName('edit-posts'));
        $this->assertFalse($role->hasPermissionByName('delete-posts'));
    }
}
