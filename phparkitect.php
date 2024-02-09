<?php

declare(strict_types=1);

use Arkitect\ClassSet;
use Arkitect\CLI\Config;
use Arkitect\Expression\ForClasses\HaveNameMatching;
use Arkitect\Expression\ForClasses\NotHaveDependencyOutsideNamespace;
use Arkitect\Expression\ForClasses\ResideInOneOfTheseNamespaces;
use Arkitect\RuleBuilders\Architecture\Architecture;
use Arkitect\Rules\Rule;

return static function (Config $config): void {
    $classSet = ClassSet::fromDir(__DIR__.'/src');

    $layeredArchitectureRules = Architecture::withComponents()
        ->component('Controller')->definedBy('App\Controller\*')
        ->component('Service')->definedBy('App\Service\*')
        ->component('Repository')->definedBy('App\Repository\*')
        ->component('Entity')->definedBy('App\Entity\*')

        ->where('Controller')->mayDependOnComponents('Service', 'Entity')
        ->where('Service')->mayDependOnComponents('Repository', 'Entity')
        ->where('Repository')->mayDependOnComponents('Entity')
        ->where('Entity')->shouldNotDependOnAnyComponent()

        ->rules();

    $serviceNamingRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\Service'))
        ->should(new HaveNameMatching('*Service'))
        ->because('we want uniform naming for services');

    $repositoryNamingRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\Repository'))
        ->should(new HaveNameMatching('*Repository'))
        ->because('we want uniform naming for repositories');

    $config->add($classSet, $serviceNamingRule, $repositoryNamingRule, ...$layeredArchitectureRules);

    /**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *
     *      CATALOG
     *
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     */

    $catalogClassSet = ClassSet::fromDir(__DIR__ . '/_catalog/src');

    $allowedPhpDependencies = require_once __DIR__ . '/tools/phparkitect/PhpDependencies/allowed_always.php';
    $allowedVendorDependenciesInCatalogCore = require_once __DIR__ . '/tools/phparkitect/VendorDependencies/allowed_in_catalog_core.php';
    $allowedVendorDependenciesInCatalogAdapters = require_once __DIR__ . '/tools/phparkitect/VendorDependencies/allowed_in_catalog_adapters.php';

    $catalogPortAndAdapterArchitectureRules = Architecture::withComponents()
        ->component('Core')->definedBy('Catalog\Core\*')
        ->component('Adapters')->definedBy('Catalog\AdapterFor*')
        ->component('Infrastructure')->definedBy('Catalog\Infrastructure\*')

        ->where('Infrastructure')->shouldNotDependOnAnyComponent()
        ->where('Adapters')->mayDependOnComponents('Core', 'Infrastructure')
        ->where('Core')->shouldNotDependOnAnyComponent()
        ->rules();


    $allowedDependenciesInCoreCode = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInCatalogCore);
    $catalogCoreIsolationRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('Catalog\Core'))
        ->should(new NotHaveDependencyOutsideNamespace('Catalog\Core', $allowedDependenciesInCoreCode))
        ->because('we want isolate our core domain from external world.');


    $allowedDependenciesInCatalogAdapters = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInCatalogAdapters);
    $catalogAdaptersIsolationRule = Rule::allClasses()
        ->except('Catalog\Adapter\Http\Web\ModuleDetailsController', 'Catalog\Adapter\Packagist\ModuleReaderOnPackagist')
        ->that(new ResideInOneOfTheseNamespaces('Catalog\Adapter*'))
        ->should(new NotHaveDependencyOutsideNamespace('Catalog\Core', $allowedDependenciesInCatalogAdapters))
        ->because('we want isolate our Adapters from ever growing dependencies.');

    $config->add($catalogClassSet, $catalogCoreIsolationRule, $catalogAdaptersIsolationRule, ...$catalogPortAndAdapterArchitectureRules);
};
