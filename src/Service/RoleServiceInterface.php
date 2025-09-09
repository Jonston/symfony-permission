<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Service;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;

interface RoleServiceInterface
{
    public function createRole(string $name, ?string $description = null): Role;

    public function updateRole(Role $role, string $name, ?string $description = null): Role;

    public function deleteRole(Role $role): void;

    public function findRoleByName(string $name): ?Role;

    public function findRoleById(int $id): ?Role;

    /**
     * @return Role[]
     */
    public function getAllRoles(): array;

    /**
     * @param string[] $names
     * @return Role[]
     */
    public function findRolesByNames(array $names): array;

    public function assignPermissionToRole(Role $role, Permission $permission): Role;

    public function revokePermissionFromRole(Role $role, Permission $permission): Role;

    /**
     * @param Permission[] $permissions
     */
    public function assignPermissionsToRole(Role $role, array $permissions): Role;

    /**
     * @param string[] $permissionNames
     */
    public function assignPermissionsByNamesToRole(Role $role, array $permissionNames): Role;

    public function revokeAllPermissionsFromRole(Role $role): Role;
}
