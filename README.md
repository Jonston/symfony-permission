# Symfony Permission Bundle

Базовый бандл для управления разрешениями и ролями в Symfony, похожий на spatie-permission, но с упрощенным функционалом.

## Установка

1. Добавьте бандл в `config/bundles.php`:
```php
<?php

return [
    // ...
    Jonston\SymfonyPermission\SymfonyPermissionBundle::class => ['all' => true],
];
```

2. Импортируйте конфигурацию Doctrine в `config/packages/doctrine.yaml`:
```yaml
imports:
    - { resource: '../../vendor/jonston/symfony-permission/src/Resources/config/doctrine.yaml' }
```

3. Создайте и выполните миграции:
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## Использование

### Создание разрешений

```php
use Jonston\SymfonyPermission\Service\PermissionServiceInterface;

class SomeController
{
    public function __construct(
        private PermissionServiceInterface $permissionService
    ) {}
    
    public function createPermission()
    {
        // Создание разрешения
        $permission = $this->permissionService->createPermission(
            'edit-posts', 
            'Разрешение на редактирование постов'
        );
        
        // Поиск разрешения
        $permission = $this->permissionService->findPermissionByName('edit-posts');
        
        // Получение всех разрешений
        $permissions = $this->permissionService->getAllPermissions();
    }
}
```

### Управление ролями

```php
use Jonston\SymfonyPermission\Service\RoleServiceInterface;

class RoleController
{
    public function __construct(
        private RoleServiceInterface $roleService
    ) {}
    
    public function createRole()
    {
        // Создание роли
        $role = $this->roleService->createRole(
            'editor', 
            'Редактор контента'
        );
        
        // Назначение разрешений роли
        $this->roleService->assignPermissionsByNamesToRole(
            $role, 
            ['edit-posts', 'create-posts']
        );
        
        // Проверка разрешения
        $hasPermission = $role->hasPermissionByName('edit-posts');
    }
}
```

## Структура базы данных

Бандл создаст следующие таблицы:
- `permissions` - таблица разрешений
- `roles` - таблица ролей  
- `role_permissions` - связующая таблица many-to-many

## Функционал

- ✅ Создание, обновление, удаление разрешений
- ✅ Создание, обновление, удаление ролей
- ✅ Назначение разрешений ролям
- ✅ Отзыв разрешений у ролей
- ✅ Проверка наличия разрешений у роли
- ✅ Следование принципам SOLID
- ✅ Использование репозиториев для работы с данными
