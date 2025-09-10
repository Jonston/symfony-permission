# Symfony Permission Bundle

A basic permission management bundle for Symfony, similar to spatie/laravel-permission. This bundle provides a simple and flexible way to manage user permissions and roles in your Symfony application.

## Features

- Role and permission management through a single service
- Polymorphic relationships (assign roles/permissions to any model)
- Direct permission assignment to models
- Permission inheritance through roles
- Simple API following SOLID principles
- Repository pattern implementation
- Easy integration with existing User entities
- Separation of concerns - management through service, checking through entity traits

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

Add the traits to your User entity depending on what functionality you need:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jonston\SymfonyPermission\Trait\HasPermissions;
use Jonston\SymfonyPermission\Trait\HasRoles;
use Jonston\SymfonyPermission\Service\PermissionServiceInterface;

#[ORM\Entity]
class User
{
    use HasPermissions, HasRoles; // Use both traits or just one if needed

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    private PermissionServiceInterface $permissionService;

    public function __construct(
        PermissionServiceInterface $permissionService
    ) {
        $this->permissionService = $permissionService;
    }

    // ... other properties

    protected function getPermissionService(): PermissionServiceInterface
    {
        return $this->permissionService;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
```

### Creating Permissions and Roles

```php
use Jonston\SymfonyPermission\Service\PermissionServiceInterface;

class PermissionSetupService
{
    public function __construct(
        private PermissionServiceInterface $permissionService
    ) {}

    public function setup(): void
    {
        // Create permissions
        $editPosts = $this->permissionService->createPermission('edit-posts');
        $deletePosts = $this->permissionService->createPermission('delete-posts');
        $viewPosts = $this->permissionService->createPermission('view-posts');

        // Create roles
        $admin = $this->permissionService->createRole('admin');
        $editor = $this->permissionService->createRole('editor');

        // Assign permissions to roles through service
        $this->permissionService->assignPermissionToRole($admin, $editPosts);
        $this->permissionService->assignPermissionToRole($admin, $deletePosts);
        $this->permissionService->assignPermissionToRole($admin, $viewPosts);

        $this->permissionService->assignPermissionsToRole($editor, [$editPosts, $viewPosts]);
    }
}
```

### Managing Roles and Permissions

**ALL management operations are done through the PermissionService:**

```php
class UserManagementService
{
    public function __construct(
        private PermissionServiceInterface $permissionService
    ) {}

    public function assignRoleToUser(User $user, string $roleName): void
    {
        $this->permissionService->assignRoleTo($user, $roleName);
    }

    public function removeRoleFromUser(User $user, string $roleName): void
    {
        $this->permissionService->removeRoleFrom($user, $roleName);
    }

    public function givePermissionToUser(User $user, string $permissionName): void
    {
        $this->permissionService->givePermissionTo($user, $permissionName);
    }

    public function revokePermissionFromUser(User $user, string $permissionName): void
    {
        $this->permissionService->revokePermissionFrom($user, $permissionName);
    }

    public function syncUserRoles(User $user, array $roleNames): void
    {
        $this->permissionService->syncRoles($user, $roleNames);
    }

    public function assignPermissionToRole(string $roleName, string $permissionName): void
    {
        $role = $this->permissionService->findRoleByName($roleName);
        $permission = $this->permissionService->findPermissionByName($permissionName);
        
        if ($role && $permission) {
            $this->permissionService->assignPermissionToRole($role, $permission);
        }
    }

    public function syncRolePermissions(string $roleName, array $permissionNames): void
    {
        $role = $this->permissionService->findRoleByName($roleName);
        if (!$role) {
            return;
        }

        $permissions = [];
        foreach ($permissionNames as $permissionName) {
            $permission = $this->permissionService->findPermissionByName($permissionName);
            if ($permission) {
                $permissions[] = $permission;
            }
        }

        $this->permissionService->syncPermissionsToRole($role, $permissions);
    }
}
```

### Checking Permissions and Roles

**Permission and role checking is done through entity trait methods:**

```php
// Check permissions (HasPermissions trait)
if ($user->hasPermissionTo('edit-posts')) {
    // User can edit posts
}

if ($user->hasDirectPermission('special-permission')) {
    // User has direct permission (not via role)
}

if ($user->hasPermissionViaRole('edit-posts')) {
    // User has permission through a role
}

$permissions = $user->getAllPermissions(); // Get all permissions (direct + via roles)

// Check roles (HasRoles trait)
if ($user->hasRole('admin')) {
    // User is admin
}

$roles = $user->getRoles(); // Get all user roles
```

### Service-based Permission Checking

**You can also check permissions through the service if needed:**

```php
// Check through service
if ($this->permissionService->hasPermission($user, 'edit-posts')) {
    // User can edit posts
}

if ($this->permissionService->hasRole($user, 'admin')) {
    // User is admin
}
```

## Architecture

### Simplified Architecture with Single Service

- **PermissionService**: One service handles ALL operations (permissions, roles, assignments, checking)
- **PermissionServiceInterface**: Interface for the service (useful for testing and dependency inversion)
- **HasPermissions trait**: Provides permission checking methods to entities
- **HasRoles trait**: Provides role checking methods to entities

### Why We Keep the Interface

The `PermissionServiceInterface` is kept because:

1. **Dependency Inversion Principle**: Your code depends on abstraction, not concrete implementation
2. **Testing**: Easy to create mocks for unit tests
3. **Flexibility**: You can swap implementations without changing dependent code
4. **Clear Contract**: Interface defines exactly what the service provides

### Separation of Concerns

- **PermissionService**: Handles ALL management operations (create, assign, revoke, sync)
- **HasPermissions trait**: Only provides read-only methods for checking permissions
- **HasRoles trait**: Only provides read-only methods for checking roles
- **Entities**: Only handle data storage and basic getters/setters
- **Repository classes**: Handle data persistence and retrieval

### Database Schema

The bundle creates the following tables:

- `permissions` - stores permission definitions
- `roles` - stores role definitions  
- `role_permission` - many-to-many relationship between roles and permissions
- `model_has_permissions` - polymorphic table for direct permission assignments
- `model_has_roles` - polymorphic table for role assignments

## Important: No Direct Entity Manipulation

```php
// ❌ WRONG - Do not call entity methods directly for management
$role->addPermission($permission);
$user->assignRole('admin');

// ✅ CORRECT - Always use PermissionService for management
$this->permissionService->assignPermissionToRole($role, $permission);
$this->permissionService->assignRoleTo($user, 'admin');

// ✅ CORRECT - Trait methods only for checking
if ($user->hasPermissionTo('edit-posts')) {
    // OK - read-only operation
}

if ($user->hasRole('admin')) {
    // OK - read-only operation  
}
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This bundle is open-sourced software licensed under the [MIT license](LICENSE).
