<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'Jonston\SymfonyPermission\Repository\PermissionRepository')]
#[ORM\Table(name: 'permissions')]
class Permission
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
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
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
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @return Collection<int, ModelHasPermission>
     */
    public function getModelHasPermissions(): Collection
    {
        return $this->modelHasPermissions;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    // Internal methods for Doctrine collection management (used by services only)

    /**
     * @internal Used by services only - do not call directly
     */
    public function addRole(Role $role): self
    {
        if ( ! $this->roles->contains($role)) {
            $this->roles->add($role);
        }
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    /**
     * @internal Used by services only - do not call directly
     */
    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    /**
     * @internal Used by services only - do not call directly
     */
    public function addModelHasPermission(ModelHasPermission $modelHasPermission): self
    {
        if ( ! $this->modelHasPermissions->contains($modelHasPermission)) {
            $this->modelHasPermissions->add($modelHasPermission);
            $modelHasPermission->setPermission($this);
        }

        return $this;
    }

    /**
     * @internal Used by services only - do not call directly
     */
    public function removeModelHasPermission(ModelHasPermission $modelHasPermission): self
    {
        if ($this->modelHasPermissions->removeElement($modelHasPermission)) {
            if ($modelHasPermission->getPermission() === $this) {
                $modelHasPermission->setPermission(null);
            }
        }

        return $this;
    }
}
