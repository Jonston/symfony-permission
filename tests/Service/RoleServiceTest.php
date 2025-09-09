<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Tests\Service;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Repository\RoleRepository;
use Jonston\SymfonyPermission\Service\PermissionServiceInterface;
use Jonston\SymfonyPermission\Service\RoleService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RoleServiceTest extends TestCase
{
    private RoleRepository|MockObject $roleRepository;
    private PermissionServiceInterface|MockObject $permissionService;
    private RoleService $roleService;

    protected function setUp(): void
    {
        $this->roleRepository = $this->createMock(RoleRepository::class);
        $this->permissionService = $this->createMock(PermissionServiceInterface::class);
        $this->roleService = new RoleService($this->roleRepository, $this->permissionService);
    }

    public function testCreateRole(): void
    {
        $name = 'admin';
        $description = 'Administrator role';

        $this->roleRepository
            ->expects($this->once())
            ->method('findByName')
            ->with($name)
            ->willReturn(null);

        $this->roleRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Role $role) use ($name, $description) {
                return $role->getName() === $name && $role->getDescription() === $description;
            }));

        $result = $this->roleService->createRole($name, $description);

        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals($name, $result->getName());
        $this->assertEquals($description, $result->getDescription());
    }

    public function testCreateRoleWithExistingName(): void
    {
        $name = 'existing-role';
        $existingRole = new Role($name);

        $this->roleRepository
            ->expects($this->once())
            ->method('findByName')
            ->with($name)
            ->willReturn($existingRole);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Role "existing-role" already exists');

        $this->roleService->createRole($name);
    }

    public function testUpdateRole(): void
    {
        $role = new Role('old-name');
        $newName = 'new-name';
        $newDescription = 'New description';

        // Set ID using reflection
        $reflection = new \ReflectionClass($role);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($role, 1);

        $this->roleRepository
            ->expects($this->once())
            ->method('findByName')
            ->with($newName)
            ->willReturn(null);

        $this->roleRepository
            ->expects($this->once())
            ->method('save')
            ->with($role);

        $result = $this->roleService->updateRole($role, $newName, $newDescription);

        $this->assertEquals($newName, $result->getName());
        $this->assertEquals($newDescription, $result->getDescription());
    }

    public function testDeleteRole(): void
    {
        $role = new Role('test-role');

        $this->roleRepository
            ->expects($this->once())
            ->method('remove')
            ->with($role);

        $this->roleService->deleteRole($role);
    }

    public function testAssignPermissionToRole(): void
    {
        $role = new Role('test-role');
        $permission = new Permission('test-permission');

        $this->roleRepository
            ->expects($this->once())
            ->method('save')
            ->with($role);

        $result = $this->roleService->assignPermissionToRole($role, $permission);

        $this->assertTrue($result->hasPermission($permission));
    }

    public function testRevokePermissionFromRole(): void
    {
        $role = new Role('test-role');
        $permission = new Permission('test-permission');
        $role->addPermission($permission);

        $this->roleRepository
            ->expects($this->once())
            ->method('save')
            ->with($role);

        $result = $this->roleService->revokePermissionFromRole($role, $permission);

        $this->assertFalse($result->hasPermission($permission));
    }

    public function testAssignPermissionsToRole(): void
    {
        $role = new Role('test-role');
        $permissions = [
            new Permission('permission1'),
            new Permission('permission2'),
        ];

        $this->roleRepository
            ->expects($this->once())
            ->method('save')
            ->with($role);

        $result = $this->roleService->assignPermissionsToRole($role, $permissions);

        foreach ($permissions as $permission) {
            $this->assertTrue($result->hasPermission($permission));
        }
    }

    public function testAssignPermissionsByNamesToRole(): void
    {
        $role = new Role('test-role');
        $permissionNames = ['permission1', 'permission2'];
        $permissions = [
            new Permission('permission1'),
            new Permission('permission2'),
        ];

        $this->permissionService
            ->expects($this->once())
            ->method('findPermissionsByNames')
            ->with($permissionNames)
            ->willReturn($permissions);

        $this->roleRepository
            ->expects($this->once())
            ->method('save')
            ->with($role);

        $result = $this->roleService->assignPermissionsByNamesToRole($role, $permissionNames);

        foreach ($permissions as $permission) {
            $this->assertTrue($result->hasPermission($permission));
        }
    }

    public function testAssignPermissionsByNamesToRoleWithNotFoundPermissions(): void
    {
        $role = new Role('test-role');
        $permissionNames = ['permission1', 'permission2', 'nonexistent'];
        $foundPermissions = [
            new Permission('permission1'),
            new Permission('permission2'),
        ];

        $this->permissionService
            ->expects($this->once())
            ->method('findPermissionsByNames')
            ->with($permissionNames)
            ->willReturn($foundPermissions);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Permissions not found: nonexistent');

        $this->roleService->assignPermissionsByNamesToRole($role, $permissionNames);
    }

    public function testRevokeAllPermissionsFromRole(): void
    {
        $role = new Role('test-role');
        $permissions = [
            new Permission('permission1'),
            new Permission('permission2'),
        ];

        foreach ($permissions as $permission) {
            $role->addPermission($permission);
        }

        $this->roleRepository
            ->expects($this->once())
            ->method('save')
            ->with($role);

        $result = $this->roleService->revokeAllPermissionsFromRole($role);

        $this->assertCount(0, $result->getPermissions());
    }

    public function testFindRoleByName(): void
    {
        $name = 'test-role';
        $role = new Role($name);

        $this->roleRepository
            ->expects($this->once())
            ->method('findByName')
            ->with($name)
            ->willReturn($role);

        $result = $this->roleService->findRoleByName($name);

        $this->assertSame($role, $result);
    }

    public function testFindRoleById(): void
    {
        $id = 1;
        $role = new Role('test-role');

        $this->roleRepository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($role);

        $result = $this->roleService->findRoleById($id);

        $this->assertSame($role, $result);
    }

    public function testGetAllRoles(): void
    {
        $roles = [
            new Role('role1'),
            new Role('role2'),
        ];

        $this->roleRepository
            ->expects($this->once())
            ->method('findAllOrderedByName')
            ->willReturn($roles);

        $result = $this->roleService->getAllRoles();

        $this->assertSame($roles, $result);
    }

    public function testFindRolesByNames(): void
    {
        $names = ['role1', 'role2'];
        $roles = [
            new Role('role1'),
            new Role('role2'),
        ];

        $this->roleRepository
            ->expects($this->once())
            ->method('findByNames')
            ->with($names)
            ->willReturn($roles);

        $result = $this->roleService->findRolesByNames($names);

        $this->assertSame($roles, $result);
    }
}
