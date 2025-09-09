<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'Jonston\SymfonyPermission\Repository\RoleRepository')]
#[ORM\Table(name: 'roles')]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $guardName = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, Permission>
     */
    #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: 'roles')]
    #[ORM\JoinTable(
        name: 'role_permission',
        joinColumns: [new ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'permission_id', referencedColumnName: 'id')]
    )]
    private Collection $permissions;

    /**
     * @var Collection<int, ModelHasRole>
     */
    #[ORM\OneToMany(mappedBy: 'role', targetEntity: ModelHasRole::class, cascade: ['persist', 'remove'])]
    private Collection $modelHasRoles;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->modelHasRoles = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    public function getGuardName(): ?string
    {
        return $this->guardName;
    }

    public function setGuardName(?string $guardName): self
    {
        $this->guardName = $guardName;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    /**
     * Check if role has specific permission (read-only operation)
     */
    public function hasPermission(string $permissionName): bool
    {
        foreach ($this->permissions as $permission) {
            if ($permission->getName() === $permissionName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, ModelHasRole>
     */
    public function getModelHasRoles(): Collection
    {
        return $this->modelHasRoles;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @internal Used by services only - do not call directly
     */
    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
            $permission->addRole($this);
        }
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    /**
     * @internal Used by services only - do not call directly
     */
    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->removeElement($permission)) {
            $permission->removeRole($this);
        }
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    /**
     * @internal Used by services only - do not call directly
     */
    public function addModelHasRole(ModelHasRole $modelHasRole): self
    {
        if (!$this->modelHasRoles->contains($modelHasRole)) {
            $this->modelHasRoles->add($modelHasRole);
            $modelHasRole->setRole($this);
        }

        return $this;
    }

    /**
     * @internal Used by services only - do not call directly
     */
    public function removeModelHasRole(ModelHasRole $modelHasRole): self
    {
        if ($this->modelHasRoles->removeElement($modelHasRole)) {
            if ($modelHasRole->getRole() === $this) {
                $modelHasRole->setRole(null);
            }
        }

        return $this;
    }
}
