<?php

namespace Jonston\SymfonyPermission\Service;

use Doctrine\Common\Collections\Collection;
use Jonston\SymfonyPermission\Contract\HasRolesInterface;
use Jonston\SymfonyPermission\Entity\Permission;

class AccessControlService
{
    private readonly PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function hasPermission(HasRolesInterface $entity, string|Permission $permission): bool
    {
        $resolvedPermission = $this->permissionService->resolvePermission($permission);

        if ($this->permissionService->hasPermission($entity, $resolvedPermission)) {
            return true;
        }

        foreach ($entity->getRoles() as $role) {
            if ($this->permissionService->hasPermission($role, $resolvedPermission)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyPermission(HasRolesInterface $entity, Collection $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($entity, $permission)) {
                return true;
            }
        }

        return false;
    }
    public function hasAllPermissions(HasRolesInterface $entity, Collection $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ( ! $this->hasPermission($entity, $permission)) {
                return false;
            }
        }

        return true;
    }
}