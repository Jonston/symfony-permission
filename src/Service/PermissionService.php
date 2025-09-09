<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Service;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Repository\PermissionRepository;

class PermissionService implements PermissionServiceInterface
{
    public function __construct(
        private readonly PermissionRepository $permissionRepository
    ) {
    }

    public function createPermission(string $name, ?string $description = null): Permission
    {
        $existingPermission = $this->permissionRepository->findByName($name);
        if ($existingPermission) {
            throw new \InvalidArgumentException(sprintf('Permission "%s" already exists', $name));
        }

        $permission = new Permission($name, $description);
        $this->permissionRepository->save($permission);

        return $permission;
    }

    public function updatePermission(Permission $permission, string $name, ?string $description = null): Permission
    {
        $existingPermission = $this->permissionRepository->findByName($name);
        if ($existingPermission && $existingPermission->getId() !== $permission->getId()) {
            throw new \InvalidArgumentException(sprintf('Permission "%s" already exists', $name));
        }

        $permission->setName($name);
        $permission->setDescription($description);
        $this->permissionRepository->save($permission);

        return $permission;
    }

    public function deletePermission(Permission $permission): void
    {
        $this->permissionRepository->remove($permission);
    }

    public function findPermissionByName(string $name): ?Permission
    {
        return $this->permissionRepository->findByName($name);
    }

    public function findPermissionById(int $id): ?Permission
    {
        return $this->permissionRepository->find($id);
    }

    public function getAllPermissions(): array
    {
        return $this->permissionRepository->findAllOrderedByName();
    }

    public function findPermissionsByNames(array $names): array
    {
        return $this->permissionRepository->findByNames($names);
    }
}
