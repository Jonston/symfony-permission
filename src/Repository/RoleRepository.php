<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Jonston\SymfonyPermission\Entity\Role;

/**
 * @extends ServiceEntityRepository<Role>
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function save(Role $role): void
    {
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();
    }

    public function remove(Role $role): void
    {
        $this->getEntityManager()->remove($role);
        $this->getEntityManager()->flush();
    }

    public function findByName(string $name): ?Role
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @return Role[]
     */
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string[] $names
     * @return Role[]
     */
    public function findByNames(array $names): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.name IN (:names)')
            ->setParameter('names', $names)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Role[]
     */
    public function findWithPermissions(): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.permissions', 'p')
            ->addSelect('p')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
