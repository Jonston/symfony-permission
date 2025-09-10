<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Service;

use Doctrine\ORM\EntityManagerInterface;
use Jonston\SymfonyPermission\Dto\Role\CreateRoleDto;
use Jonston\SymfonyPermission\Dto\Role\SearchRoleDto;
use Jonston\SymfonyPermission\Dto\Role\UpdateRoleDto;
use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Repository\RoleRepository;

class RoleService
{
    private readonly RoleRepository $roleRepository;
    private readonly EntityManagerInterface $entityManager;

    public function __construct(RoleRepository $roleRepository, EntityManagerInterface $entityManager)
    {
        $this->roleRepository = $roleRepository;
        $this->entityManager = $entityManager;
    }

    public function createRole(CreateRoleDto $data): Role
    {
        $role = new Role();
        $role->setName($data->name);
        $role->setDescription($data->description);
        $this->entityManager->persist($role);
        $this->entityManager->flush();
        return $role;
    }

    public function updateRole(Role $role, UpdateRoleDto $data): Role
    {
        $role->setName($data->name);
        $role->setDescription($data->description);
        $this->entityManager->flush();
        return $role;
    }

    public function deleteRole(Role $role): void
    {
        $this->entityManager->remove($role);
        $this->entityManager->flush();
    }

    public function findRole(SearchRoleDto $params): ?Role
    {
        $criteria = [];
        if ($params->name !== null) {
            $criteria['name'] = $params->name;
        }
        return $this->roleRepository->findOneBy($criteria);
    }

    public function getAllRoles(): array
    {
        return $this->roleRepository->findAllOrderedByName();
    }
}
