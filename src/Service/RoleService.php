<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Service;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Repository\RoleRepository;

class RoleService implements RoleServiceInterface
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
        private readonly PermissionServiceInterface $permissionService
    ) {
    }

    public function createRole(string $name, ?string $description = null): Role
    {
        $existingRole = $this->roleRepository->findByName($name);
        if ($existingRole) {
            throw new \InvalidArgumentException(sprintf('Role "%s" already exists', $name));
        }

        $role = new Role($name, $description);
        $this->roleRepository->save($role);

        return $role;
    }

    public function updateRole(Role $role, string $name, ?string $description = null): Role
    {
        $existingRole = $this->roleRepository->findByName($name);
        if ($existingRole && $existingRole->getId() !== $role->getId()) {
            throw new \InvalidArgumentException(sprintf('Role "%s" already exists', $name));
        }

        $role->setName($name);
        $role->setDescription($description);
        $this->roleRepository->save($role);

        return $role;
    }

    public function deleteRole(Role $role): void
    {
        $this->roleRepository->remove($role);
    }

    public function findRoleByName(string $name): ?Role
    {
        return $this->roleRepository->findByName($name);
    }

    public function findRoleById(int $id): ?Role
    {
        return $this->roleRepository->find($id);
    }

    public function getAllRoles(): array
    {
        return $this->roleRepository->findAllOrderedByName();
    }

    public function findRolesByNames(array $names): array
    {
        return $this->roleRepository->findByNames($names);
    }

    public function assignPermissionToRole(Role $role, Permission $permission): Role
    {
        $role->addPermission($permission);
        $this->roleRepository->save($role);

        return $role;
    }

    public function revokePermissionFromRole(Role $role, Permission $permission): Role
    {
        $role->removePermission($permission);
        $this->roleRepository->save($role);

        return $role;
    }

    public function assignPermissionsToRole(Role $role, array $permissions): Role
    {
        foreach ($permissions as $permission) {
            $role->addPermission($permission);
        }
        $this->roleRepository->save($role);

        return $role;
    }

    public function assignPermissionsByNamesToRole(Role $role, array $permissionNames): Role
    {
        $permissions = $this->permissionService->findPermissionsByNames($permissionNames);

        $foundNames = array_map(fn(Permission $p) => $p->getName(), $permissions);
        $notFoundNames = array_diff($permissionNames, $foundNames);

        if (!empty($notFoundNames)) {
            throw new \InvalidArgumentException(sprintf(
                'Permissions not found: %s',
                implode(', ', $notFoundNames)
            ));
        }

        return $this->assignPermissionsToRole($role, $permissions);
    }

    public function revokeAllPermissionsFromRole(Role $role): Role
    {
        foreach ($role->getPermissions() as $permission) {
            $role->removePermission($permission);
        }
        $this->roleRepository->save($role);

        return $role;
    }
}
