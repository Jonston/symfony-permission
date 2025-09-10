<?php

namespace Jonston\SymfonyPermission\Tests\Service;

use Jonston\SymfonyPermission\Dto\Role\CreateRoleDto;
use Jonston\SymfonyPermission\Dto\Role\UpdateRoleDto;
use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Repository\RoleRepository;
use Jonston\SymfonyPermission\Service\RoleService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class RoleServiceTest extends TestCase
{
    private RoleService $service;
    private RoleRepository $repository;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RoleRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new RoleService($this->repository, $this->em);
    }

    public function testCreateRole(): void
    {
        $dto = new CreateRoleDto('admin', 'desc');
        $role = $this->service->createRole($dto);
        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('admin', $role->getName());
        $this->assertEquals('desc', $role->getDescription());
    }

    public function testUpdateRole(): void
    {
        $role = new Role();
        $role->setName('old');
        $dto = new UpdateRoleDto('new', 'newdesc');
        $updated = $this->service->updateRole($role, $dto);
        $this->assertEquals('new', $updated->getName());
        $this->assertEquals('newdesc', $updated->getDescription());
    }

    public function testDeleteRole(): void
    {
        $role = new Role();
        $this->em->expects($this->once())->method('remove')->with($role);
        $this->em->expects($this->once())->method('flush');
        $this->service->deleteRole($role);
    }
}

