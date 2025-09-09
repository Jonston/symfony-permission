<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission;

use Jonston\SymfonyPermission\DependencyInjection\SymfonyPermissionExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SymfonyPermissionBundle extends Bundle
{
    public function getContainerExtension(): SymfonyPermissionExtension
    {
        return new SymfonyPermissionExtension();
    }
}
