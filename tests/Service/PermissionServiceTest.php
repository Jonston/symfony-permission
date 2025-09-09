<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Repository\PermissionRepository;
use Jonston\SymfonyPermission\Service\PermissionService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PermissionServiceTest extends TestCase
{
    private PermissionService $permissionService;
    private EntityManagerInterface|MockObject $entityManager;
    private PermissionRepository|MockObject $permissionRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->permissionRepository = $this->createMock(PermissionRepository::class);

        $this->permissionService = new PermissionService(
            $this->entityManager,
            $this->permissionRepository
        );
    }

    public function testCreatePermission(): void
    {
        $this->entityManager->expects($this->once())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        $permission = $this->permissionService->create('test-permission', 'web');

        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('test-permission', $permission->getName());
        $this->assertEquals('web', $permission->getGuardName());
    }

    public function testFindByName(): void
    {
        $permission = new Permission();
        $permission->setName('test-permission');

        $this->permissionRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'test-permission'])
            ->willReturn($permission);

        $result = $this->permissionService->findByName('test-permission');

        $this->assertEquals($permission, $result);
    }

    public function testGetAll(): void
    {
        $permissions = [new Permission(), new Permission()];

        $this->permissionRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($permissions);

        $result = $this->permissionService->getAll();

        $this->assertEquals($permissions, $result);
    }

    public function testDeletePermission(): void
    {
        $permission = new Permission();

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($permission);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->permissionService->delete($permission);
    }

    public function testGivePermissionToModel(): void
    {
        $model = new class {
            private int $id = 1;
            public function getId(): int { return $this->id; }
        };

        $permission = new Permission();
        $permission->setName('test-permission');

        $this->permissionRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'test-permission'])
            ->willReturn($permission);

        $modelHasPermissionRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $modelHasPermissionRepo->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null); // No existing assignment

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($modelHasPermissionRepo);

        $this->entityManager->expects($this->once())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->permissionService->givePermissionTo($model, 'test-permission');
    }

    public function testGivePermissionToModelThrowsExceptionForNonExistentPermission(): void
    {
        $model = new class {
            private int $id = 1;
            public function getId(): int { return $this->id; }
        };

        $this->permissionRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'non-existent'])
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Permission 'non-existent' not found");

        $this->permissionService->givePermissionTo($model, 'non-existent');
    }
}
