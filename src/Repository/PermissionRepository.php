<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Jonston\SymfonyPermission\Entity\Permission;

/**
 * @extends ServiceEntityRepository<Permission>
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }
}
