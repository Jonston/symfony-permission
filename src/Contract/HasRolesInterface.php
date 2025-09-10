<?php

namespace Jonston\SymfonyPermission\Contract;

use Doctrine\Common\Collections\Collection;
use Jonston\SymfonyPermission\Entity\Role;

interface HasRolesInterface extends HasPermissionsInterface
{
    public function getRoles(): Collection;

    public function hasRole(Role $role): bool;

    public function addRole(Role $role): void;

    public function removeRole(Role $role): void;
}