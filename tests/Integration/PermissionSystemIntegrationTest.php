<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Tests\Integration;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Service\PermissionService;
use Jonston\SymfonyPermission\Service\RoleService;
use PHPUnit\Framework\TestCase;

class PermissionSystemIntegrationTest extends TestCase
{
    private PermissionService $permissionService;
    private RoleService $roleService;

    protected function setUp(): void
    {
        // This is a simplified test without actual Doctrine setup
        // In real integration tests, you would set up a test database
        $this->markTestSkipped('Integration tests require database setup');
    }

    public function testPermissionCreationAndRetrieval(): void
    {
        $permission = $this->permissionService->create('test-permission');

        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('test-permission', $permission->getName());

        $retrievedPermission = $this->permissionService->findByName('test-permission');
        $this->assertEquals($permission->getId(), $retrievedPermission->getId());
    }

    public function testRoleCreationAndRetrieval(): void
    {
        $role = $this->roleService->create('test-role');

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('test-role', $role->getName());

        $retrievedRole = $this->roleService->findByName('test-role');
        $this->assertEquals($role->getId(), $retrievedRole->getId());
    }

    public function testRolePermissionRelationships(): void
    {
        $permission = $this->permissionService->create('edit-posts');
        $role = $this->roleService->create('editor');

        $role->addPermission($permission);
        $this->roleService->update($role);

        $this->assertTrue($role->hasPermission('edit-posts'));
        $this->assertTrue($role->getPermissions()->contains($permission));
        $this->assertTrue($permission->getRoles()->contains($role));
    }

    public function testModelPermissionAssignment(): void
    {
        // Mock model for testing
        $model = new class {
            private int $id = 1;
            public function getId(): int { return $this->id; }
        };

        $permission = $this->permissionService->create('special-permission');

        $this->permissionService->givePermissionTo($model, 'special-permission');

        $this->assertTrue($this->permissionService->hasPermission($model, 'special-permission'));
        $this->assertTrue($this->permissionService->hasDirectPermission($model, 'special-permission'));
    }

    public function testModelRoleAssignment(): void
    {
        // Mock model for testing
        $model = new class {
            private int $id = 1;
            public function getId(): int { return $this->id; }
        };

        $role = $this->roleService->create('admin');

        $this->roleService->assignRoleTo($model, 'admin');

        $this->assertTrue($this->roleService->hasRole($model, 'admin'));
        $this->assertContains('admin', $this->roleService->getModelRoles($model));
    }

    public function testPermissionInheritanceViaRole(): void
    {
        // Mock model for testing
        $model = new class {
            private int $id = 1;
            public function getId(): int { return $this->id; }
        };

        $permission = $this->permissionService->create('manage-users');
        $role = $this->roleService->create('manager');

        $role->addPermission($permission);
        $this->roleService->update($role);

        $this->roleService->assignRoleTo($model, 'manager');

        $this->assertTrue($this->permissionService->hasPermissionViaRole($model, 'manage-users'));
        $this->assertTrue($this->permissionService->hasPermission($model, 'manage-users'));
    }
}
