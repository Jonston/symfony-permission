<?php

namespace Jonston\SymfonyPermission\Trait;

use Doctrine\Common\Collections\Collection;
use Jonston\SymfonyPermission\Entity\Permission;

trait HasPermissions
{
    protected Collection $permissions;

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): void
    {
        if (!$this->hasPermission($permission)) {
            $this->permissions->add($permission);
        }
    }

    public function removePermission(Permission $permission): void
    {
        if ($this->hasPermission($permission)) {
            $this->permissions->removeElement($permission);
        }
    }

    public function hasPermission(Permission $permission): bool
    {
        return $this->permissions->contains($permission);
    }
}
