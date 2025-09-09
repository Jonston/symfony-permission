<?php

namespace Jonston\SymfonyPermission\Service;

use Doctrine\ORM\EntityManagerInterface;
use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;

class RolePermissionService implements RolePermissionServiceInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function assignPermissionToRole(Role $role, Permission $permission): void
    {
        if (!$role->getPermissions()->contains($permission)) {
            $role->addPermission($permission);
            $this->entityManager->flush();
        }
    }

    public function revokePermissionFromRole(Role $role, Permission $permission): void
    {
        if ($role->getPermissions()->contains($permission)) {
            $role->removePermission($permission);
            $this->entityManager->flush();
        }
    }

    public function assignPermissionsToRole(Role $role, array $permissions): void
    {
        foreach ($permissions as $permission) {
            if (!$role->getPermissions()->contains($permission)) {
                $role->addPermission($permission);
            }
        }
        $this->entityManager->flush();
    }

    public function syncPermissionsToRole(Role $role, array $permissions): void
    {
        // Remove all existing permissions
        $existingPermissions = $role->getPermissions()->toArray();
        foreach ($existingPermissions as $permission) {
            $role->removePermission($permission);
        }

        // Add new permissions
        foreach ($permissions as $permission) {
            $role->addPermission($permission);
        }

        $this->entityManager->flush();
    }

    public function revokeAllPermissionsFromRole(Role $role): void
    {
        $permissions = $role->getPermissions()->toArray();
        foreach ($permissions as $permission) {
            $role->removePermission($permission);
        }
        $this->entityManager->flush();
    }
}
