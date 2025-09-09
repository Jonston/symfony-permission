<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Tests\Integration;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Service\PermissionServiceInterface;
use Jonston\SymfonyPermission\Service\RoleServiceInterface;
use PHPUnit\Framework\TestCase;

class PermissionSystemIntegrationTest extends TestCase
{
    public function testCompletePermissionWorkflow(): void
    {
        // This is a basic integration test without database
        // In real scenario, you would use Symfony's KernelTestCase with database setup

        $permission1 = new Permission('create-posts', 'Can create posts');
        $permission2 = new Permission('edit-posts', 'Can edit posts');
        $permission3 = new Permission('delete-posts', 'Can delete posts');

        $editorRole = new Role('editor', 'Content editor');
        $adminRole = new Role('admin', 'Administrator');

        // Test role-permission assignments
        $editorRole->addPermission($permission1);
        $editorRole->addPermission($permission2);

        $adminRole->addPermission($permission1);
        $adminRole->addPermission($permission2);
        $adminRole->addPermission($permission3);

        // Test editor permissions
        $this->assertTrue($editorRole->hasPermissionByName('create-posts'));
        $this->assertTrue($editorRole->hasPermissionByName('edit-posts'));
        $this->assertFalse($editorRole->hasPermissionByName('delete-posts'));
        $this->assertCount(2, $editorRole->getPermissions());

        // Test admin permissions
        $this->assertTrue($adminRole->hasPermissionByName('create-posts'));
        $this->assertTrue($adminRole->hasPermissionByName('edit-posts'));
        $this->assertTrue($adminRole->hasPermissionByName('delete-posts'));
        $this->assertCount(3, $adminRole->getPermissions());

        // Test permission revocation
        $editorRole->removePermission($permission1);
        $this->assertFalse($editorRole->hasPermissionByName('create-posts'));
        $this->assertCount(1, $editorRole->getPermissions());

        // Test role name updates
        $originalUpdatedAt = $editorRole->getUpdatedAt();
        usleep(1000);
        $editorRole->setName('senior-editor');
        $this->assertEquals('senior-editor', $editorRole->getName());
        $this->assertGreaterThan($originalUpdatedAt, $editorRole->getUpdatedAt());
    }

    public function testPermissionDuplicationPrevention(): void
    {
        $role = new Role('test-role');
        $permission = new Permission('test-permission');

        // Add same permission multiple times
        $role->addPermission($permission);
        $role->addPermission($permission);
        $role->addPermission($permission);

        // Should only have one instance
        $this->assertCount(1, $role->getPermissions());
        $this->assertTrue($role->hasPermission($permission));
    }

    public function testRolePermissionRelationships(): void
    {
        $permission = new Permission('shared-permission');
        $role1 = new Role('role1');
        $role2 = new Role('role2');

        $role1->addPermission($permission);
        $role2->addPermission($permission);

        // Both roles should have the permission
        $this->assertTrue($role1->hasPermission($permission));
        $this->assertTrue($role2->hasPermission($permission));

        // Permission should be associated with both roles
        $this->assertTrue($permission->getRoles()->contains($role1));
        $this->assertTrue($permission->getRoles()->contains($role2));

        // Remove permission from one role
        $role1->removePermission($permission);
        $this->assertFalse($role1->hasPermission($permission));
        $this->assertTrue($role2->hasPermission($permission));
    }
}
