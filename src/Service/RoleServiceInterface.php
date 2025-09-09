<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Service;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;

interface RoleServiceInterface
{
    public function create(string $name, ?string $guardName = null): Role;

    public function findByName(string $name): ?Role;

    public function findById(int $id): ?Role;

    public function getAll(): array;

    public function update(Role $role): Role;

    public function delete(Role $role): void;

    public function assignRoleTo(object $model, string $roleName): void;

    public function removeRoleFrom(object $model, string $roleName): void;

    public function hasRole(object $model, string $roleName): bool;

    public function getModelRoles(object $model): array;

    public function syncRoles(object $model, array $roleNames): void;
}
