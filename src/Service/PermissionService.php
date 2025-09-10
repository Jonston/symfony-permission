<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Service;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Jonston\SymfonyPermission\Contract\HasPermissionsInterface;
use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Repository\PermissionRepository;

class PermissionService
{
    private readonly PermissionRepository $permissionRepository;
    private readonly EntityManagerInterface $entityManager;

    public function __construct(
        PermissionRepository $permissionRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->permissionRepository = $permissionRepository;
        $this->entityManager = $entityManager;
    }

    public function createPermission(CreatePermissionDto $data): Permission
    {
        $permission = new Permission();
        $permission->setName($data->name);
        $this->entityManager->persist($permission);
        $this->entityManager->flush();

        return $permission;
    }

    public function updatePermission(Permission $permission, UpdatePermissionDto $data): Permission
    {
    }

    public function deletePermission(Permission $permission): void
    {

    }

    public function findPermission(SerachPermissionDto $params): ?Permission
    {
    }

    public function getAllPermissions(): array
    {
        return $this->permissionRepository->findAllOrderedByName();
    }

    public function assignPermissionTo(
        HasPermissionsInterface $entity,
        string|Permission $permissions
    ): void
    {
    }

    public function assignPermissionsTo(
        HasPermissionsInterface $entity,
        array|Collection $permissions
    ): void
    {
    }

    public function revokePermissionFrom(
        HasPermissionsInterface $entity,
        string|Permission $permissions
    ): void
    {
    }

    public function revokePermissionsFrom(
        HasPermissionsInterface $entity,
        array|Collection $permissions
    ): void
    {
    }

    public function hasAllPermissions(
        HasPermissionsInterface $entity,
        string|Permission|Collection $permissions
    ): bool
    {

    }

    public function hasPermission(
        HasPermissionsInterface $entity,
        string|Permission $permissions
    ): bool
    {
    }

    public function hasAnyPermission(
        HasPermissionsInterface $entity,
        array|Collection $permissions
    ): bool
    {
    }

    public function resolvePermission(string|Permission $permissions): Permission
    {
    }
}
