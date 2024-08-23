<?php

namespace App;

use BffApi\Infrastructure\Framework\Extension\BffApiModuleExtension;
use BffWeb\Infrastructure\Framework\Extension\BffWebModuleExtension;
use Catalog\Infrastructure\Framework\Extension\CatalogModuleExtension;
use Metadata\Infrastructure\Framework\Extension\MetadataModuleExtension;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->registerExtension(new BffApiModuleExtension());
        $container->registerExtension(new BffWebModuleExtension());
        $container->registerExtension(new CatalogModuleExtension());
        $container->registerExtension(new MetadataModuleExtension());
    }
}
