<?php

namespace Jonston\SymfonyPermission\Trait;

use Doctrine\Common\Collections\Collection;
use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;

trait HasRoles
{
    use HasPermissions;

    protected Collection $roles;

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function hasRole(Role $role): bool
    {
        return $this->roles->contains($role);
    }

    public function addRole(Role $role): void
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            foreach ($role->getPermissions() as $permission) {
                $this->addPermission($permission);
            }
        }
    }

    public function removeRole(Role $role): void
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
            foreach ($role->getPermissions() as $permission) {
                $this->removePermission($permission);
            }
        }
    }
}
