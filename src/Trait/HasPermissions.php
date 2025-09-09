<?php

namespace Jonston\SymfonyPermission\Trait;

use Jonston\SymfonyPermission\Service\PermissionServiceInterface;
use Jonston\SymfonyPermission\Service\RoleServiceInterface;

trait HasPermissions
{
    /**
     * Check if the model has a specific permission
     */
    public function hasPermissionTo(string $permission): bool
    {
        return $this->getPermissionService()->hasPermission($this, $permission);
    }

    /**
     * Check if the model has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->getRoleService()->hasRole($this, $role);
    }

    /**
     * Get all roles assigned to this model
     */
    public function getRoles(): array
    {
        return $this->getRoleService()->getModelRoles($this);
    }

    /**
     * Get all permissions assigned to this model (direct + via roles)
     */
    public function getAllPermissions(): array
    {
        return $this->getPermissionService()->getModelPermissions($this);
    }

    /**
     * Check if the model has direct permission (not via role)
     */
    public function hasDirectPermission(string $permission): bool
    {
        return $this->getPermissionService()->hasDirectPermission($this, $permission);
    }

    /**
     * Check if the model has permission via role
     */
    public function hasPermissionViaRole(string $permission): bool
    {
        return $this->getPermissionService()->hasPermissionViaRole($this, $permission);
    }

    /**
     * This method should be implemented by the entity using this trait
     * to provide access to the PermissionService
     */
    abstract protected function getPermissionService(): PermissionServiceInterface;

    /**
     * This method should be implemented by the entity using this trait
     * to provide access to the RoleService
     */
    abstract protected function getRoleService(): RoleServiceInterface;
}
