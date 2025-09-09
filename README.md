# Symfony Permission Bundle

A basic bundle for managing permissions and roles in Symfony, similar to spatie-permission, but with simplified functionality.

## Installation

### Via Composer

```bash
composer require jonston/symfony-permission
```

1. Add the bundle to `config/bundles.php`:
```php
<?php

return [
    // ...
    Jonston\SymfonyPermission\SymfonyPermissionBundle::class => ['all' => true],
];
```

2. Import Doctrine configuration in `config/packages/doctrine.yaml`:
```yaml
imports:
    - { resource: '../../vendor/jonston/symfony-permission/src/Resources/config/doctrine.yaml' }
```

3. Create and run migrations:
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## Usage

### Creating permissions

```php
use Jonston\SymfonyPermission\Service\PermissionServiceInterface;

class SomeController
{
    public function __construct(
        private PermissionServiceInterface $permissionService
    ) {}
    
    public function createPermission()
    {
        // Create a permission
        $permission = $this->permissionService->createPermission(
            'edit-posts', 
            'Permission to edit posts'
        );
        
        // Find a permission
        $permission = $this->permissionService->findPermissionByName('edit-posts');
        
        // Get all permissions
        $permissions = $this->permissionService->getAllPermissions();
    }
}
```

### Managing roles

```php
use Jonston\SymfonyPermission\Service\RoleServiceInterface;

class RoleController
{
    public function __construct(
        private RoleServiceInterface $roleService
    ) {}
    
    public function createRole()
    {
        // Create a role
        $role = $this->roleService->createRole(
            'editor', 
            'Content editor'
        );
        
        // Assign permissions to the role
        $this->roleService->assignPermissionsByNamesToRole(
            $role, 
            ['edit-posts', 'create-posts']
        );
        
        // Check permission
        $hasPermission = $role->hasPermissionByName('edit-posts');
    }
}
```

## Database structure

The bundle will create the following tables:
- `permissions` - permissions table
- `roles` - roles table
- `role_permissions` - many-to-many pivot table

## Features

- ✅ Create, update, delete permissions
- ✅ Create, update, delete roles
- ✅ Assign permissions to roles
- ✅ Revoke permissions from roles
- ✅ Check if a role has a permission
- ✅ Follows SOLID principles
- ✅ Uses repositories for data access
