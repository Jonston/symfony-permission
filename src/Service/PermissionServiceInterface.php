<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Service;

use Jonston\SymfonyPermission\Entity\Permission;

interface PermissionServiceInterface
{
    public function create(string $name, ?string $guardName = null): Permission;

    public function findByName(string $name): ?Permission;

    public function findById(int $id): ?Permission;

    public function getAll(): array;

    public function update(Permission $permission): Permission;

    public function delete(Permission $permission): void;

    public function givePermissionTo(object $model, string $permissionName): void;

    public function revokePermissionFrom(object $model, string $permissionName): void;

    public function hasPermission(object $model, string $permissionName): bool;

    public function hasDirectPermission(object $model, string $permissionName): bool;

    public function hasPermissionViaRole(object $model, string $permissionName): bool;

    public function getModelPermissions(object $model): array;
}
