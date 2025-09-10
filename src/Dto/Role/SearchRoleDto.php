<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Dto\Role;

use Jonston\SymfonyPermission\Dto\AbstractDto;
use Symfony\Component\Validator\Constraints as Assert;

class SearchRoleDto extends AbstractDto
{
    #[Assert\Length(max:255)]
    public readonly ?string $name;

    public function __construct(
        ?string $name = null,
        public readonly ?string $description = null
    ) {
        $this->name = $name;
    }
}
