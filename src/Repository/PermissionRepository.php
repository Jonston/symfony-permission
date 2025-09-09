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

    public function findByName(string $name): ?Permission
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @param string[] $names
     * @return Permission[]
     */
    public function findByNames(array $names): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name IN (:names)')
            ->setParameter('names', $names)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Permission[]
     */
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(Permission $permission): void
    {
        $this->getEntityManager()->persist($permission);
        $this->getEntityManager()->flush();
    }

    public function remove(Permission $permission): void
    {
        $this->getEntityManager()->remove($permission);
        $this->getEntityManager()->flush();
    }
}
