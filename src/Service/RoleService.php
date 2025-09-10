<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Service;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Repository\RoleRepository;

class RoleService
{
    private readonly RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }
}
