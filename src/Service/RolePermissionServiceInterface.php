<?php

namespace Jonston\SymfonyPermission\Service;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;

interface RolePermissionServiceInterface
{
    public function assignPermissionToRole(Role $role, Permission $permission): void;

    public function revokePermissionFromRole(Role $role, Permission $permission): void;

    public function assignPermissionsToRole(Role $role, array $permissions): void;

    public function syncPermissionsToRole(Role $role, array $permissions): void;

    public function revokeAllPermissionsFromRole(Role $role): void;
}
