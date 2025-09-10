

A flexible and extensible permission and role management bundle for Symfony applications, inspired by [spatie/laravel-permission](https://github.com/spatie/laravel-permission). This package provides a robust foundation for role and permission management, with a clean architecture and full test coverage.

## Concept & Inspiration
This bundle is inspired by the popular Spatie Laravel Permission package. It brings similar concepts to Symfony: roles, permissions, traits for your models, DTOs for safe data transfer, and services for business logic. The goal is to keep your domain clean, decoupled, and easy to extend.

## Integration Guide: How to Use in Your Application

### 1. Install the Bundle
```bash
composer require jonston/symfony-permission
```

### 2. Enable the Bundle
Add to `config/bundles.php`:
```php
Jonston\SymfonyPermission\SymfonyPermissionBundle::class => ['all' => true],
```

### 3. Run Doctrine Migrations
```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### 4. Implement in Your User Entity
Add traits and interfaces to your User entity:
```php
use Jonston\SymfonyPermission\Trait\HasRoles;
use Jonston\SymfonyPermission\Contract\HasRolesInterface;

class User implements HasRolesInterface
{
    use HasRoles;
    // ... your fields ...
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    // ... your logic ...
}
```

### 5. Usage Examples

#### Create Roles and Permissions
```php
use Jonston\SymfonyPermission\Dto\Role\CreateRoleDto;
use Jonston\SymfonyPermission\Dto\Permission\CreatePermissionDto;

$role = $roleService->createRole(new CreateRoleDto('admin', 'Administrator role'));
$permission = $permissionService->createPermission(new CreatePermissionDto('edit articles', 'Can edit articles'));
```

#### Assign/Revoke Roles and Permissions to User
```php
$user->addRole($role);
$user->addPermission($permission);
$user->removeRole($role);
$user->removePermission($permission);
```

#### Assign/Revoke Permissions to Role
```php
$role->addPermission($permission);
$role->removePermission($permission);
```

#### Check Permissions (Direct or via Role)
```php
$accessControlService->hasPermission($user, 'edit articles'); // true if user or any role has permission
$accessControlService->hasAnyPermission($user, [$permission1, $permission2]);
$accessControlService->hasAllPermissions($user, [$permission1, $permission2]);
```

#### Use in Controllers or Security Voters
```php
if ($accessControlService->hasPermission($user, 'delete articles')) {
    // allow action
}
