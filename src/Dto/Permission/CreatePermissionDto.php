<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Dto\Permission;

use Jonston\SymfonyPermission\Dto\AbstractDto;

class CreatePermissionDto extends AbstractDto
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null
    ) {}
}