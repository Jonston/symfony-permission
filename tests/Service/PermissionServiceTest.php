<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Tests\Service;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Repository\PermissionRepository;
use Jonston\SymfonyPermission\Service\PermissionService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PermissionServiceTest extends TestCase
{
    private PermissionRepository|MockObject $permissionRepository;
    private PermissionService $permissionService;

    protected function setUp(): void
    {
        $this->permissionRepository = $this->createMock(PermissionRepository::class);
        $this->permissionService = new PermissionService($this->permissionRepository);
    }

    public function testCreatePermission(): void
    {
        $name = 'edit-posts';
        $description = 'Permission to edit posts';

        $this->permissionRepository
            ->expects($this->once())
            ->method('findByName')
            ->with($name)
            ->willReturn(null);

        $this->permissionRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Permission $permission) use ($name, $description) {
                return $permission->getName() === $name && $permission->getDescription() === $description;
            }));

        $result = $this->permissionService->createPermission($name, $description);

        $this->assertInstanceOf(Permission::class, $result);
        $this->assertEquals($name, $result->getName());
        $this->assertEquals($description, $result->getDescription());
    }

    public function testCreatePermissionWithoutDescription(): void
    {
        $name = 'delete-posts';

        $this->permissionRepository
            ->expects($this->once())
            ->method('findByName')
            ->with($name)
            ->willReturn(null);

        $this->permissionRepository
            ->expects($this->once())
            ->method('save');

        $result = $this->permissionService->createPermission($name);

        $this->assertEquals($name, $result->getName());
        $this->assertNull($result->getDescription());
    }

    public function testCreatePermissionWithExistingName(): void
    {
        $name = 'existing-permission';
        $existingPermission = new Permission($name);

        $this->permissionRepository
            ->expects($this->once())
            ->method('findByName')
            ->with($name)
            ->willReturn($existingPermission);

        $this->permissionRepository
            ->expects($this->never())
            ->method('save');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Permission "existing-permission" already exists');

        $this->permissionService->createPermission($name);
    }

    public function testUpdatePermission(): void
    {
        $permission = new Permission('old-name');
        $newName = 'new-name';
        $newDescription = 'New description';

        // Mock reflection to set ID for the permission
        $reflection = new \ReflectionClass($permission);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($permission, 1);

        $this->permissionRepository
            ->expects($this->once())
            ->method('findByName')
            ->with($newName)
            ->willReturn(null);

        $this->permissionRepository
            ->expects($this->once())
            ->method('save')
            ->with($permission);

        $result = $this->permissionService->updatePermission($permission, $newName, $newDescription);

        $this->assertEquals($newName, $result->getName());
        $this->assertEquals($newDescription, $result->getDescription());
    }

    public function testUpdatePermissionWithExistingName(): void
    {
        $permission = new Permission('old-name');
        $existingPermission = new Permission('existing-name');

        // Set different IDs
        $reflection = new \ReflectionClass($permission);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($permission, 1);

        $reflection = new \ReflectionClass($existingPermission);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($existingPermission, 2);

        $this->permissionRepository
            ->expects($this->once())
            ->method('findByName')
            ->with('existing-name')
            ->willReturn($existingPermission);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Permission "existing-name" already exists');

        $this->permissionService->updatePermission($permission, 'existing-name');
    }

    public function testDeletePermission(): void
    {
        $permission = new Permission('test-permission');

        $this->permissionRepository
            ->expects($this->once())
            ->method('remove')
            ->with($permission);

        $this->permissionService->deletePermission($permission);
    }

    public function testFindPermissionByName(): void
    {
        $name = 'test-permission';
        $permission = new Permission($name);

        $this->permissionRepository
            ->expects($this->once())
            ->method('findByName')
            ->with($name)
            ->willReturn($permission);

        $result = $this->permissionService->findPermissionByName($name);

        $this->assertSame($permission, $result);
    }

    public function testFindPermissionById(): void
    {
        $id = 1;
        $permission = new Permission('test-permission');

        $this->permissionRepository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($permission);

        $result = $this->permissionService->findPermissionById($id);

        $this->assertSame($permission, $result);
    }

    public function testGetAllPermissions(): void
    {
        $permissions = [
            new Permission('permission1'),
            new Permission('permission2'),
        ];

        $this->permissionRepository
            ->expects($this->once())
            ->method('findAllOrderedByName')
            ->willReturn($permissions);

        $result = $this->permissionService->getAllPermissions();

        $this->assertSame($permissions, $result);
    }

    public function testFindPermissionsByNames(): void
    {
        $names = ['permission1', 'permission2'];
        $permissions = [
            new Permission('permission1'),
            new Permission('permission2'),
        ];

        $this->permissionRepository
            ->expects($this->once())
            ->method('findByNames')
            ->with($names)
            ->willReturn($permissions);

        $result = $this->permissionService->findPermissionsByNames($names);

        $this->assertSame($permissions, $result);
    }
}
