<?php

namespace Jonston\SymfonyPermission\Tests\Service;

use Jonston\SymfonyPermission\Dto\Permission\CreatePermissionDto;
use Jonston\SymfonyPermission\Dto\Permission\UpdatePermissionDto;
use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Repository\PermissionRepository;
use Jonston\SymfonyPermission\Service\PermissionService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PermissionServiceTest extends TestCase
{
    private PermissionService $service;
    private PermissionRepository $repository;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PermissionRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new PermissionService($this->repository, $this->em);
    }

    public function testCreatePermission(): void
    {
        $dto = new CreatePermissionDto('edit', 'desc');
        $permission = $this->service->createPermission($dto);
        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('edit', $permission->getName());
        $this->assertEquals('desc', $permission->getDescription());
    }

    public function testUpdatePermission(): void
    {
        $permission = new Permission();
        $permission->setName('old');
        $dto = new UpdatePermissionDto('new', 'newdesc');
        $updated = $this->service->updatePermission($permission, $dto);
        $this->assertEquals('new', $updated->getName());
        $this->assertEquals('newdesc', $updated->getDescription());
    }

    public function testDeletePermission(): void
    {
        $permission = new Permission();
        $this->em->expects($this->once())->method('remove')->with($permission);
        $this->em->expects($this->once())->method('flush');
        $this->service->deletePermission($permission);
    }
}

