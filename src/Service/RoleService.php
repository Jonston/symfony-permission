<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Service;

use Doctrine\ORM\EntityManagerInterface;
use Jonston\SymfonyPermission\Entity\ModelHasRole;
use Jonston\SymfonyPermission\Entity\Role;
use Jonston\SymfonyPermission\Repository\RoleRepository;

class RoleService implements RoleServiceInterface
{
    private EntityManagerInterface $entityManager;
    private RoleRepository $roleRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        RoleRepository $roleRepository
    ) {
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
    }

    public function create(string $name, ?string $guardName = null): Role
    {
        $role = new Role();
        $role->setName($name);
        if ($guardName) {
            $role->setGuardName($guardName);
        }

        $this->entityManager->persist($role);
        $this->entityManager->flush();

        return $role;
    }

    public function findByName(string $name): ?Role
    {
        return $this->roleRepository->findOneBy(['name' => $name]);
    }

    public function findById(int $id): ?Role
    {
        return $this->roleRepository->find($id);
    }

    public function getAll(): array
    {
        return $this->roleRepository->findAll();
    }

    public function update(Role $role): Role
    {
        $this->entityManager->flush();
        return $role;
    }

    public function delete(Role $role): void
    {
        $this->entityManager->remove($role);
        $this->entityManager->flush();
    }

    /**
     * Assign role to a model
     */
    public function assignRoleTo(object $model, string $roleName): void
    {
        $role = $this->findByName($roleName);
        if (!$role) {
            throw new \InvalidArgumentException("Role '{$roleName}' not found");
        }

        $existing = $this->entityManager->getRepository(ModelHasRole::class)
            ->findOneBy([
                'role' => $role,
                'modelType' => get_class($model),
                'modelId' => $model->getId()
            ]);

        if ($existing) {
            return;
        }

        $modelHasRole = new ModelHasRole();
        $modelHasRole->setRole($role);
        $modelHasRole->setModelType(get_class($model));
        $modelHasRole->setModelId($model->getId());

        $this->entityManager->persist($modelHasRole);
        $this->entityManager->flush();
    }

    /**
     * Remove role from a model
     */
    public function removeRoleFrom(object $model, string $roleName): void
    {
        $role = $this->findByName($roleName);
        if (!$role) {
            return;
        }

        $modelHasRole = $this->entityManager->getRepository(ModelHasRole::class)
            ->findOneBy([
                'role' => $role,
                'modelType' => get_class($model),
                'modelId' => $model->getId()
            ]);

        if ($modelHasRole) {
            $this->entityManager->remove($modelHasRole);
            $this->entityManager->flush();
        }
    }

    /**
     * Check if model has role
     */
    public function hasRole(object $model, string $roleName): bool
    {
        $role = $this->findByName($roleName);
        if (!$role) {
            return false;
        }

        $modelHasRole = $this->entityManager->getRepository(ModelHasRole::class)
            ->findOneBy([
                'role' => $role,
                'modelType' => get_class($model),
                'modelId' => $model->getId()
            ]);

        return $modelHasRole !== null;
    }

    /**
     * Get all roles for a model
     */
    public function getModelRoles(object $model): array
    {
        $modelHasRoles = $this->entityManager->getRepository(ModelHasRole::class)
            ->findBy([
                'modelType' => get_class($model),
                'modelId' => $model->getId()
            ]);

        return array_map(fn($mhr) => $mhr->getRole()->getName(), $modelHasRoles);
    }

    /**
     * Sync roles for a model (remove all existing and assign new ones)
     */
    public function syncRoles(object $model, array $roleNames): void
    {
        // Remove all existing roles
        $existingModelHasRoles = $this->entityManager->getRepository(ModelHasRole::class)
            ->findBy([
                'modelType' => get_class($model),
                'modelId' => $model->getId()
            ]);

        foreach ($existingModelHasRoles as $modelHasRole) {
            $this->entityManager->remove($modelHasRole);
        }

        // Assign new roles
        foreach ($roleNames as $roleName) {
            $role = $this->findByName($roleName);
            if ($role) {
                $modelHasRole = new ModelHasRole();
                $modelHasRole->setRole($role);
                $modelHasRole->setModelType(get_class($model));
                $modelHasRole->setModelId($model->getId());
                $this->entityManager->persist($modelHasRole);
            }
        }

        $this->entityManager->flush();
    }
}
