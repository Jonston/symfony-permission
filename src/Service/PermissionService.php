<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Service;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Jonston\SymfonyPermission\Contract\HasPermissionsInterface;
use Jonston\SymfonyPermission\Dto\Permission\CreatePermissionDto;
use Jonston\SymfonyPermission\Dto\Permission\SerachPermissionDto;
use Jonston\SymfonyPermission\Dto\Permission\UpdatePermissionDto;
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
        $permission->setDescription($data->description);
        $this->entityManager->persist($permission);
        $this->entityManager->flush();

        return $permission;
    }

    public function updatePermission(Permission $permission, UpdatePermissionDto $data): Permission
    {
        $permission->setName($data->name);
        $permission->setDescription($data->description);
        $this->entityManager->flush();
        return $permission;
    }

    public function deletePermission(Permission $permission): void
    {
        $this->entityManager->remove($permission);
        $this->entityManager->flush();
    }

    public function findPermission(SerachPermissionDto $params): ?Permission
    {
        $criteria = [];

        if ($params->name !== null) {
            $criteria['name'] = $params->name;
        }

        return $this->permissionRepository->findOneBy($criteria);
    }

    public function assignPermissionTo(
        HasPermissionsInterface $entity,
        string|Permission $permission
    ): void
    {
        $permissionEntity = $this->resolvePermission($permission);
        $entity->addPermission($permissionEntity);
        $this->entityManager->flush();
    }

    public function assignPermissionsTo(
        HasPermissionsInterface $entity,
        array|Collection $permissions
    ): void
    {
        foreach ($permissions as $permission) {
            $this->assignPermissionTo($entity, $permission);
        }
    }

    public function revokePermissionFrom(
        HasPermissionsInterface $entity,
        string|Permission $permission
    ): void
    {
        $permissionEntity = $this->resolvePermission($permission);
        $entity->removePermission($permissionEntity);
        $this->entityManager->flush();
    }

    public function revokePermissionsFrom(
        HasPermissionsInterface $entity,
        array|Collection $permissions
    ): void
    {
        foreach ($permissions as $permission) {
            $this->revokePermissionFrom($entity, $permission);
        }
    }

    public function hasAllPermissions(
        HasPermissionsInterface $entity,
        string|Permission|Collection $permissions
    ): bool
    {
        if ($permissions instanceof Collection || is_array($permissions)) {
            foreach ($permissions as $permission) {
                if (!$this->hasPermission($entity, $permission)) {
                    return false;
                }
            }
            return true;
        }
        return $this->hasPermission($entity, $permissions);
    }

    public function hasPermission(
        HasPermissionsInterface $entity,
        string|Permission $permission
    ): bool
    {
        $permissionEntity = $this->resolvePermission($permission);
        return $entity->hasPermission($permissionEntity);
    }

    public function hasAnyPermission(
        HasPermissionsInterface $entity,
        array|Collection $permissions
    ): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($entity, $permission)) {
                return true;
            }
        }
        return false;
    }

    public function resolvePermission(string|Permission $permission): Permission
    {
        if ($permission instanceof Permission) {
            return $permission;
        }
        $permissionEntity = $this->permissionRepository->findOneBy(['name' => $permission]);
        if (!$permissionEntity) {
            throw new \InvalidArgumentException("Permission '{$permission}' not found");
        }
        return $permissionEntity;
    }
}
