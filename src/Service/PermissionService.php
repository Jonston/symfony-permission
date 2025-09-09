<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Service;

use Jonston\SymfonyPermission\Entity\Permission;
use Jonston\SymfonyPermission\Repository\PermissionRepository;

class PermissionService implements PermissionServiceInterface
{
    public function __construct(
        private readonly PermissionRepository $permissionRepository
    ) {
    private EntityManagerInterface $entityManager;
    private PermissionRepository $permissionRepository;

    }
        EntityManagerInterface $entityManager,
        PermissionRepository $permissionRepository
    {
        $this->entityManager = $entityManager;
        $this->permissionRepository = $permissionRepository;
        $existingPermission = $this->permissionRepository->findByName($name);
        if ($existingPermission && $existingPermission->getId() !== $permission->getId()) {
    public function create(string $name, ?string $guardName = null): Permission
        }
        $permission = new Permission();
        return $permission;
        if ($guardName) {
            $permission->setGuardName($guardName);
        }

        $this->entityManager->persist($permission);
        $this->entityManager->flush();
    public function deletePermission(Permission $permission): void
    {
        $this->permissionRepository->remove($permission);
    }
    public function findByName(string $name): ?Permission
    public function findPermissionByName(string $name): ?Permission
        return $this->permissionRepository->findOneBy(['name' => $name]);
        return $this->permissionRepository->findByName($name);
    }
    public function findById(int $id): ?Permission
    public function getAllPermissions(): array
    {
        return $this->permissionRepository->findAllOrderedByName();
    }
    public function getAll(): array
    public function findPermissionsByNames(array $names): array
        return $this->permissionRepository->findAll();
        return $this->permissionRepository->findByNames($names);
    }
    public function update(Permission $permission): Permission

        $this->entityManager->flush();
        return $permission;
    }

    public function delete(Permission $permission): void
    {
        $this->entityManager->remove($permission);
        $this->entityManager->flush();
