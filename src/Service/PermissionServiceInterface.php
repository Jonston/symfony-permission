<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Service;

use Jonston\SymfonyPermission\Entity\Permission;

interface PermissionServiceInterface
{
    public function createPermission(string $name, ?string $description = null): Permission;

    public function updatePermission(Permission $permission, string $name, ?string $description = null): Permission;

    public function deletePermission(Permission $permission): void;

    public function findPermissionByName(string $name): ?Permission;

    public function findPermissionById(int $id): ?Permission;

    /**
     * @return Permission[]
     */
    public function getAllPermissions(): array;

    /**
     * @param string[] $names
     * @return Permission[]
     */
    public function findPermissionsByNames(array $names): array;
}
