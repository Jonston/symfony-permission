<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\DependencyInjection;

use Jonston\SymfonyPermission\Repository\PermissionRepository;
use Jonston\SymfonyPermission\Repository\RoleRepository;
use Jonston\SymfonyPermission\Service\PermissionService;
use Jonston\SymfonyPermission\Service\PermissionServiceInterface;
use Jonston\SymfonyPermission\Service\RoleService;
use Jonston\SymfonyPermission\Service\RoleServiceInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SymfonyPermissionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        // Register repositories
        $container->autowire(PermissionRepository::class)
            ->setPublic(false)
            ->addTag('doctrine.repository_service');

        $container->autowire(RoleRepository::class)
            ->setPublic(false)
            ->addTag('doctrine.repository_service');

        // Register services
        $container->autowire(PermissionService::class)
            ->setPublic(false);

        $container->autowire(RoleService::class)
            ->setPublic(false);

        // Register service interfaces
        $container->setAlias(PermissionServiceInterface::class, PermissionService::class);
        $container->setAlias(RoleServiceInterface::class, RoleService::class);
    }
}
