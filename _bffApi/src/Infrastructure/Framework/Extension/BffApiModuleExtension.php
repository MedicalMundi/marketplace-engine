<?php declare(strict_types=1);

/**
 * This file is part of the medicalmundi/marketplace-engine
 *
 * @copyright (c) 2024 MedicalMundi
 *
 * This software consists of voluntary contributions made by many individuals
 * {@link https://github.com/medicalmundi/marketplace-engine/graphs/contributors developer} and is licensed under the MIT license.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @license https://github.com/MedicalMundi/marketplace-engine/blob/main/LICENSE MIT
 */

namespace BffApi\Infrastructure\Framework\Extension;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class BffApiModuleExtension extends AbstractExtension
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->booleanNode('enabled')
            ->info('Enable or disable the module.')
            ->defaultValue(true)->end()

            ->ScalarNode('catalog_dir')
            ->info('The path of the catalogs directory.')
            ->defaultValue('%kernel.project_dir%/var')->end()

            ->ScalarNode('catalog_filename')
            ->info('Filename of the default catalog.')
            ->defaultValue('catalog.json')->end()

            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->parameters()
            ->set('module_bff_api.enabled', $config['enabled'])
            ->set('module_bff_api.catalog_dir', $config['catalog_dir'])
            ->set('module_bff_api.catalog_filename', $config['catalog_filename'])
        ;
    }

    public function getAlias(): string
    {
        return 'module_bff_api';
    }
}
