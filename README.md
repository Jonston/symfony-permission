# Symfony Permission Bundle

A basic permission management bundle for Symfony, similar to spatie/laravel-permission. This bundle provides a simple and flexible way to manage user permissions and roles in your Symfony application.

## Features

- Role and permission management
- Polymorphic relationships (assign roles/permissions to any model)
- Direct permission assignment to models
- Permission inheritance through roles
- Simple API following SOLID principles
- Repository pattern implementation
- Easy integration with existing User entities
- Complete separation of concerns - ALL management through services, checking through entity methods

## Requirements

- PHP 8.1 or higher
- Symfony 6.0+ or 7.0+
- Doctrine ORM 2.10+ or 3.0+

## Installation

Install the bundle via Composer:

```bash
composer require jonston/symfony-permission
```

### Register the Bundle

If you're using Symfony Flex, the bundle will be registered automatically. Otherwise, add it to your `bundles.php`:

```php
// config/bundles.php
return [
    // ...
    Jonston\SymfonyPermission\SymfonyPermissionBundle::class => ['all' => true],
];
```

### Update Database Schema

Run the following commands to create the necessary database tables:

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

Or if you're not using migrations:

```bash
php bin/console doctrine:schema:update --force
```

## Usage

### Basic Setup

First, add the `HasPermissions` trait to your User entity:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jonston\SymfonyPermission\Trait\HasPermissions;
use Jonston\SymfonyPermission\Service\PermissionServiceInterface;
use Jonston\SymfonyPermission\Service\RoleServiceInterface;

#[ORM\Entity]
class User
{
    use HasPermissions;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    private PermissionServiceInterface $permissionService;
    private RoleServiceInterface $roleService;

    public function __construct(
        PermissionServiceInterface $permissionService,
        RoleServiceInterface $roleService
    ) {
        $this->permissionService = $permissionService;
        $this->roleService = $roleService;
    }

    // ... other properties

    protected function getPermissionService(): PermissionServiceInterface
    {
        return $this->permissionService;
    }

    protected function getRoleService(): RoleServiceInterface
    {
        return $this->roleService;
    }
}
```

### Creating Permissions and Roles

```php
use Jonston\SymfonyPermission\Service\PermissionServiceInterface;
use Jonston\SymfonyPermission\Service\RoleServiceInterface;
use Jonston\SymfonyPermission\Service\RolePermissionServiceInterface;

class PermissionSetupService
{
    public function __construct(
        private PermissionServiceInterface $permissionService,
        private RoleServiceInterface $roleService,
        private RolePermissionServiceInterface $rolePermissionService
    ) {}

    public function setup(): void
    {
        // Create permissions
        $editPosts = $this->permissionService->create('edit-posts');
        $deletePosts = $this->permissionService->create('delete-posts');
        $viewPosts = $this->permissionService->create('view-posts');

        // Create roles
        $admin = $this->roleService->create('admin');
        $editor = $this->roleService->create('editor');

        // Assign permissions to roles through service (NOT directly on entities)
        $this->rolePermissionService->assignPermissionToRole($admin, $editPosts);
        $this->rolePermissionService->assignPermissionToRole($admin, $deletePosts);
        $this->rolePermissionService->assignPermissionToRole($admin, $viewPosts);

        $this->rolePermissionService->assignPermissionsToRole($editor, [$editPosts, $viewPosts]);
    }
}
```

### Managing Roles and Permissions

**ALL management operations MUST be done through services:**

```php
class UserManagementService
{
    public function __construct(
        private PermissionServiceInterface $permissionService,
        private RoleServiceInterface $roleService,
        private RolePermissionServiceInterface $rolePermissionService
    ) {}

    public function assignRoleToUser(User $user, string $roleName): void
    {
        // Assign role to user through service
        $this->roleService->assignRoleTo($user, $roleName);
    }

    public function removeRoleFromUser(User $user, string $roleName): void
    {
        // Remove role from user through service
        $this->roleService->removeRoleFrom($user, $roleName);
    }

    public function givePermissionToUser(User $user, string $permissionName): void
    {
        // Give direct permission to user through service
        $this->permissionService->givePermissionTo($user, $permissionName);
    }

    public function revokePermissionFromUser(User $user, string $permissionName): void
    {
        // Revoke permission from user through service
        $this->permissionService->revokePermissionFrom($user, $permissionName);
    }

    public function syncUserRoles(User $user, array $roleNames): void
    {
        // Sync all user roles through service
        $this->roleService->syncRoles($user, $roleNames);
    }
}
```

### Managing Role-Permission Relationships

```php
class RoleManagementService 
{
    public function __construct(
        private RolePermissionServiceInterface $rolePermissionService,
        private PermissionServiceInterface $permissionService,
        private RoleServiceInterface $roleService
    ) {}

    public function assignPermissionToRole(string $roleName, string $permissionName): void
    {
        $role = $this->roleService->findByName($roleName);
        $permission = $this->permissionService->findByName($permissionName);
        
        if ($role && $permission) {
            $this->rolePermissionService->assignPermissionToRole($role, $permission);
        }
    }

    public function revokePermissionFromRole(string $roleName, string $permissionName): void
    {
        $role = $this->roleService->findByName($roleName);
        $permission = $this->permissionService->findByName($permissionName);
        
        if ($role && $permission) {
            $this->rolePermissionService->revokePermissionFromRole($role, $permission);
        }
    }

    public function syncRolePermissions(string $roleName, array $permissionNames): void
    {
        $role = $this->roleService->findByName($roleName);
        if (!$role) {
            return;
        }

        $permissions = [];
        foreach ($permissionNames as $permissionName) {
            $permission = $this->permissionService->findByName($permissionName);
            if ($permission) {
                $permissions[] = $permission;
            }
        }

        $this->rolePermissionService->syncPermissionsToRole($role, $permissions);
    }
}
```

### Checking Permissions

**Permission checking is done through entity methods (read-only operations):**

```php
// Check if user has permission
if ($user->hasPermissionTo('edit-posts')) {
    // User can edit posts
}

// Check if user has role
if ($user->hasRole('admin')) {
    // User is admin
}

// Check if user has direct permission (not via role)
if ($user->hasDirectPermission('special-permission')) {
    // User has direct permission
}

// Check if user has permission via role
if ($user->hasPermissionViaRole('edit-posts')) {
    // User has permission through a role
}

// Get all user permissions
$permissions = $user->getAllPermissions();

// Get all user roles
$roles = $user->getRoles();
```

### Service-based Permission Checking

**You can also check permissions through services if needed:**

```php
// Check if user has permission
if ($this->permissionService->hasPermission($user, 'edit-posts')) {
    // User can edit posts
}

// Check if user has role
if ($this->roleService->hasRole($user, 'admin')) {
    // User is admin
}
```

### Database Schema

The bundle creates the following tables:

- `permissions` - stores permission definitions
- `roles` - stores role definitions  
- `role_permission` - many-to-many relationship between roles and permissions
- `model_has_permissions` - polymorphic table for direct permission assignments
- `model_has_roles` - polymorphic table for role assignments

## Architecture

This bundle follows SOLID principles:

- **Single Responsibility**: Each service handles one specific concern. Entities only handle permission checking, not management.
- **Open/Closed**: Easy to extend without modifying existing code
- **Liskov Substitution**: Services implement interfaces
- **Interface Segregation**: Focused interfaces for different responsibilities
- **Dependency Inversion**: Depends on abstractions, not concretions

### Separation of Concerns

- **Entities**: Only provide read-only methods for checking permissions and roles. Management methods are marked @internal.
- **PermissionService**: Handles permission CRUD operations and direct permission assignments to models
- **RoleService**: Handles role CRUD operations and role assignments to models
- **RolePermissionService**: Handles assignments of permissions to roles
- **Repository classes**: Handle data persistence and retrieval

### Important: No Direct Entity Manipulation

```php
// ❌ WRONG - Do not call entity methods directly for management
$role->addPermission($permission);
$user->assignRole('admin');

// ✅ CORRECT - Always use services for management
$this->rolePermissionService->assignPermissionToRole($role, $permission);
$this->roleService->assignRoleTo($user, 'admin');

// ✅ CORRECT - Entity methods only for checking
if ($user->hasPermissionTo('edit-posts')) {
    // OK - read-only operation
}
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This bundle is open-sourced software licensed under the [MIT license](LICENSE).
