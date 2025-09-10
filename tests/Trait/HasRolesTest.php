<?php

namespace Jonston\SymfonyPermission\Tests\Trait;

use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Trait\HasRoles;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

class DummyHasRoles
{
    use HasRoles;
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }
    public function addRole(Role $role): void
    {
        if (!$this->hasRole($role)) {
            $this->roles->add($role);
        }
    }
    public function removeRole(Role $role): void
    {
        if ($this->hasRole($role)) {
            $this->roles->removeElement($role);
        }
    }
}

class HasRolesTest extends TestCase
{
    public function testAddRemoveHasRole(): void
    {
        $entity = new DummyHasRoles();
        $role = new Role();
        $role->setName('admin');
        $entity->addRole($role);
        $this->assertTrue($entity->hasRole($role));
        $entity->removeRole($role);
        $this->assertFalse($entity->hasRole($role));
    }
}

