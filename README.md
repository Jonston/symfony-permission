# Symfony Permission Bundle

A flexible and extensible permission and role management bundle for Symfony applications, inspired by [spatie/laravel-permission](https://github.com/spatie/laravel-permission).

## Concept & Paradigm
This bundle brings the best practices of access control from Spatie Laravel Permission to Symfony:
- **Explicit modeling**: Permissions and roles are first-class entities, not just strings.
- **Flexible relationships**: Users (or any domain entity) can have many roles and permissions, and roles aggregate permissions.
- **Traits & interfaces**: Easily add permission/role logic to your models via traits and interfaces.
- **DTOs & services**: All business logic and data transfer are handled via DTOs and service classes, keeping your domain clean.
- **Doctrine attributes**: All relations are described via Doctrine attributes for correct migrations and schema generation.

## Step-by-step Integration Guide

### 1. Install the Bundle
```bash
composer require jonston/symfony-permission
```

### 2. Configure Your Models

#### User Entity Example
Add traits, interfaces, and Doctrine relations:
```php
use Doctrine\ORM\Mapping as ORM;
use Jonston\SymfonyPermission\Trait\HasRoles;
use Jonston\SymfonyPermission\Contract\HasRolesInterface;
use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Entity\Permission;

#[ORM\Entity]
class User implements HasRolesInterface
{
    use HasRoles;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[ORM\JoinTable(name: 'user_role')]
    protected Collection $roles;

    #[ORM\ManyToMany(targetEntity: Permission::class)]
    #[ORM\JoinTable(name: 'user_permission')]
    protected Collection $permissions;

    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    // ... your logic ...
}
```

#### Role Entity Example
Already provided by the bundle, but you can extend:
```php
#[ORM\Entity]
class Role implements HasPermissionsInterface
{
    use HasPermissions;
    // ...existing code...
    #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: 'roles')]
    #[ORM\JoinTable(name: 'role_permission')]
    protected Collection $permissions;
    // ...existing code...
}
```

#### Permission Entity Example
Already provided by the bundle, but you can extend:
```php
#[ORM\Entity]
class Permission
{
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
    private Collection $roles;
    // ...existing code...
}
```

### 3. Run Doctrine Migrations
After configuring your models, generate and apply migrations:
```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```
This will create all necessary tables and relations: `roles`, `permissions`, `user_role`, `user_permission`, `role_permission`.

### 4. Use DTOs and Services for Business Logic
- Use DTOs (e.g. `CreateRoleDto`, `UpdatePermissionDto`) for all input to services.
- Use `RoleService` and `PermissionService` for CRUD operations and assignment logic.

#### Example: Creating and Assigning Roles/Permissions
```php
$role = $roleService->createRole(new CreateRoleDto('admin', 'Administrator role'));
$permission = $permissionService->createPermission(new CreatePermissionDto('edit articles', 'Can edit articles'));
$user->addRole($role);
$user->addPermission($permission);
$role->addPermission($permission);
```

#### Example: Revoking Roles/Permissions
```php
$user->removeRole($role);
$user->removePermission($permission);
$role->removePermission($permission);
```

#### Example: Checking Permissions
```php
$accessControlService->hasPermission($user, 'edit articles'); // true if user or any role has permission
$accessControlService->hasAnyPermission($user, [$permission1, $permission2]);
$accessControlService->hasAllPermissions($user, [$permission1, $permission2]);
```

### 5. Advanced Use Cases
- **Multiple models**: You can add HasRoles/HasPermissions to any entity (Team, Organization, etc).
- **Custom queries**: Use repositories for advanced queries (e.g. find all users with a specific permission).
- **Validation**: DTOs support Symfony Validator attributes for future validation needs.
- **Extending entities**: Add custom fields, methods, or override logic as needed.

### 6. Best Practices
- Keep all business logic in services, not in entities.
- Use DTOs for all input to services.
- Use traits and interfaces for your domain models.
- Write tests for your business logic and permission checks.
- Use Doctrine attributes for all relations to ensure correct migrations.

## Example Use Case Scenarios
- **RBAC for admin panel**: Assign roles like `admin`, `editor`, `viewer` to users, and granular permissions to roles.
- **Feature toggles**: Use permissions to enable/disable features for specific users or groups.
- **Multi-tenancy**: Assign roles/permissions to teams or organizations, not just users.
- **API access control**: Check permissions in controllers, security voters, or middleware.

## License
MIT

## Author
Jonston
