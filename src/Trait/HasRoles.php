<?php

namespace Jonston\SymfonyPermission\Trait;

use Doctrine\Common\Collections\Collection;
use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;

trait HasRoles
{
    use HasPermissions;

    protected Collection $roles;

    /** @return Collection<Role> */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function hasRole(Role $role): bool
    {
        return $this->roles->contains($role);
    }
}
