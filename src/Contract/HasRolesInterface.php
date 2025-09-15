<?php

namespace Jonston\SymfonyPermission\Contract;

use Jonston\SymfonyPermission\Entity\Role;

interface HasRolesInterface extends HasPermissionsInterface
{
    public function getRoles(): mixed; // Return mixed type to allow compatibility with UserInterface

    public function hasRole(Role $role): bool;

    public function addRole(Role $role): void;

    public function removeRole(Role $role): void;
}