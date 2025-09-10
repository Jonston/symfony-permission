<?php

namespace Jonston\SymfonyPermission\Contract;

use Doctrine\Common\Collections\Collection;
use Jonston\SymfonyPermission\Entity\Permission;

interface HasPermissionsInterface
{
    public function hasPermission(Permission $permissionName): bool;

    public function getPermissions(): Collection;

    public function addPermission(Permission $permissionName): void;

    public function removePermission(Permission $permissionName): void;
}