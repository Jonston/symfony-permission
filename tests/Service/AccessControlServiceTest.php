<?php

namespace Jonston\SymfonyPermission\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Service\AccessControlService;
use Jonston\SymfonyPermission\Service\PermissionService;
use Jonston\SymfonyPermission\Contract\HasRolesInterface;
use PHPUnit\Framework\TestCase;

class AccessControlServiceTest extends TestCase
{
    public function testHasPermissionDirectAndViaRole(): void
    {
        $permission = new Permission();
        $permission->setName('edit');
        $role = new Role();
        $role->setName('admin');
        $role->addPermission($permission);

        $entity = $this->createMock(HasRolesInterface::class);
        $entity->method('getPermissions')->willReturn(new ArrayCollection());
        $entity->method('getRoles')->willReturn(new ArrayCollection([$role]));
        $entity->method('hasPermission')->willReturn(false);
        $entity->method('hasRole')->willReturn(true);

        $permissionService = $this->createMock(PermissionService::class);
        $permissionService->method('resolvePermission')->willReturn($permission);
        $permissionService->method('hasPermission')->willReturnCallback(function($obj, $perm) use ($role, $permission) {
            return $obj === $role && $perm === $permission;
        });

        $acs = new AccessControlService($permissionService);
        $this->assertTrue($acs->hasPermission($entity, $permission));
    }

    public function testHasAnyPermission(): void
    {
        $permission1 = new Permission();
        $permission1->setName('edit');
        $permission2 = new Permission();
        $permission2->setName('view');
        $role = new Role();
        $role->setName('admin');
        $role->addPermission($permission2);

        $entity = $this->createMock(HasRolesInterface::class);
        $entity->method('getPermissions')->willReturn(new ArrayCollection());
        $entity->method('getRoles')->willReturn(new ArrayCollection([$role]));
        $entity->method('hasPermission')->willReturn(false);
        $entity->method('hasRole')->willReturn(true);

        $permissionService = $this->createMock(PermissionService::class);
        $permissionService->method('resolvePermission')->willReturnCallback(function($perm) { return $perm; });
        $permissionService->method('hasPermission')->willReturnCallback(function($obj, $perm) use ($role, $permission2) {
            return $obj === $role && $perm === $permission2;
        });

        $acs = new AccessControlService($permissionService);
        $permissions = new ArrayCollection([$permission1, $permission2]);
        $this->assertTrue($acs->hasAnyPermission($entity, $permissions));
    }

    public function testHasAllPermissions(): void
    {
        $permission1 = new Permission();
        $permission1->setName('edit');
        $permission2 = new Permission();
        $permission2->setName('view');
        $role = new Role();
        $role->setName('admin');
        $role->addPermission($permission1);
        $role->addPermission($permission2);

        $entity = $this->createMock(HasRolesInterface::class);
        $entity->method('getPermissions')->willReturn(new ArrayCollection());
        $entity->method('getRoles')->willReturn(new ArrayCollection([$role]));
        $entity->method('hasPermission')->willReturn(false);
        $entity->method('hasRole')->willReturn(true);

        $permissionService = $this->createMock(PermissionService::class);
        $permissionService->method('resolvePermission')->willReturnCallback(function($perm) { return $perm; });
        $permissionService->method('hasPermission')->willReturnCallback(function($obj, $perm) use ($role, $permission1, $permission2) {
            return $obj === $role && ($perm === $permission1 || $perm === $permission2);
        });

        $acs = new AccessControlService($permissionService);
        $permissions = new ArrayCollection([$permission1, $permission2]);
        $this->assertTrue($acs->hasAllPermissions($entity, $permissions));
    }
}

