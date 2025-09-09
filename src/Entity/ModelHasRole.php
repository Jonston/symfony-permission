<?php

namespace Jonston\SymfonyPermission\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'model_has_roles')]
#[ORM\Index(columns: ['model_type', 'model_id'])]
class ModelHasRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'modelHasRoles')]
    #[ORM\JoinColumn(nullable: false)]
    private Role $role;

    #[ORM\Column(type: 'string', length: 255)]
    private string $modelType;

    #[ORM\Column(type: 'integer')]
    private int $modelId;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getModelType(): string
    {
        return $this->modelType;
    }

    public function setModelType(string $modelType): self
    {
        $this->modelType = $modelType;
        return $this;
    }

    public function getModelId(): int
    {
        return $this->modelId;
    }

    public function setModelId(int $modelId): self
    {
        $this->modelId = $modelId;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
