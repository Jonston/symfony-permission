<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Jonston\SymfonyPermission\Contract\HasPermissionsInterface;
use Jonston\SymfonyPermission\Trait\HasPermissions;

#[ORM\Entity(repositoryClass: 'Jonston\SymfonyPermission\Repository\RoleRepository')]
#[ORM\Table(name: 'roles')]
class Role implements HasPermissionsInterface
{
    use HasPermissions;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $description;

    /**
     * @var Collection<int, Permission>
     */
    #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: 'roles')]
    #[ORM\JoinTable(
        name: 'role_permission',
        joinColumns: [new ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'permission_id', referencedColumnName: 'id')]
    )]
    protected Collection $permissions;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->description = null;
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
