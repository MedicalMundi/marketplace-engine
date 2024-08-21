<?php declare(strict_types=1);

use Arkitect\ClassSet;
use Arkitect\CLI\Config;
use Arkitect\Expression\ForClasses\DependsOnlyOnTheseNamespaces;
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

        ->where('Infrastructure')->mayDependOnComponents('Core')
        ->where('Adapters')->mayDependOnComponents('Core', 'Infrastructure')
        ->where('Core')->shouldNotDependOnAnyComponent()
        ->rules();


    $allowedDependenciesInCoreCode = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInCatalogCore);
    $catalogCoreIsolationRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('Catalog\Core'))
        ->should(new NotHaveDependencyOutsideNamespace('Catalog\Core', $allowedDependenciesInCoreCode))
        ->because('we want isolate our catalog core domain from external world.');


    $allowedDependenciesInCatalogAdapters = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInCatalogAdapters);
    $catalogAdaptersIsolationRule = Rule::allClasses()
        ->except('Catalog\Adapter\Http\Web\ModuleDetailsController', 'Catalog\Adapter\Packagist\ModuleReaderOnPackagist')
        ->that(new ResideInOneOfTheseNamespaces('Catalog\Adapter*'))
        ->should(new NotHaveDependencyOutsideNamespace('Catalog\Core', $allowedDependenciesInCatalogAdapters))
        ->because('we want isolate our catalog Adapters from ever growing dependencies.');

    $config->add($catalogClassSet, $catalogCoreIsolationRule, $catalogAdaptersIsolationRule, ...$catalogPortAndAdapterArchitectureRules);


    /**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *
     *      BffWeb
     *
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     */

    $bffWebClassSet = ClassSet::fromDir(__DIR__ . '/_bffWeb/src');

    $allowedVendorDependenciesInBffWebCore = require_once __DIR__ . '/tools/phparkitect/VendorDependencies/allowed_in_bffWeb_core.php';
    $allowedVendorDependenciesInBffWebAdapters = require_once __DIR__ . '/tools/phparkitect/VendorDependencies/allowed_in_bffWeb_adapters.php';

    $bffWebPortAndAdapterArchitectureRules = Architecture::withComponents()
        ->component('Core')->definedBy('BffWeb\Core\*')
        ->component('Adapters')->definedBy('BffWeb\Adapter*')
        ->component('Infrastructure')->definedBy('BffWeb\Infrastructure\*')

        ->where('Infrastructure')->shouldNotDependOnAnyComponent()
        ->where('Adapters')->mayDependOnComponents('Core', 'Infrastructure')
        ->where('Core')->shouldNotDependOnAnyComponent()
        ->rules();


    $allowedDependenciesInBffWebCore = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInBffWebCore);
    $bffWebCoreIsolationRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('BffWeb\Core'))
        ->should(new NotHaveDependencyOutsideNamespace('BbfWeb\Core', $allowedDependenciesInBffWebCore))
        ->because('we want isolate our bffWeb core domain from external world.');


    $allowedDependenciesInBffWebAdapters = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInBffWebAdapters);
    $bffWebAdaptersIsolationRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('BffWeb\Adapter*'))
        ->should(new NotHaveDependencyOutsideNamespace('BffWeb\Adapter*', $allowedDependenciesInBffWebAdapters))
        ->because('we want isolate our bffWeb Adapters from ever growing dependencies.');

    $config->add($bffWebClassSet, $bffWebCoreIsolationRule, $bffWebAdaptersIsolationRule, ...$bffWebPortAndAdapterArchitectureRules);


    /**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *
     *      BffApi
     *
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     */

    $bffApiClassSet = ClassSet::fromDir(__DIR__ . '/_bffApi/src');

    $allowedVendorDependenciesInBffApiCore = require_once __DIR__ . '/tools/phparkitect/VendorDependencies/allowed_in_bffApi_core.php';
    $allowedVendorDependenciesInBffApiAdapters = require_once __DIR__ . '/tools/phparkitect/VendorDependencies/allowed_in_bffApi_adapters.php';

    $bffApiPortAndAdapterArchitectureRules = Architecture::withComponents()
        ->component('Core')->definedBy('BffApi\Core\*')
        ->component('Adapters')->definedBy('BffApi\Adapter*')
        ->component('Infrastructure')->definedBy('BffApi\Infrastructure\*')

        ->where('Infrastructure')->shouldNotDependOnAnyComponent()
        ->where('Adapters')->mayDependOnComponents('Core', 'Infrastructure')
        ->where('Core')->shouldNotDependOnAnyComponent()
        ->rules();


    $allowedDependenciesInBffApiCore = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInBffApiCore);
    $bffApiCoreIsolationRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('BffApi\Core'))
        ->should(new NotHaveDependencyOutsideNamespace('BffApi\Core', $allowedDependenciesInBffApiCore))
        ->because('we want isolate our bffApi core domain from external world.');


    $allowedDependenciesInBffApiAdapters = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInBffApiAdapters);
    $bffApiAdaptersIsolationRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('BffApi\Adapter*'))
        ->should(new NotHaveDependencyOutsideNamespace('BffApi\Adapter*', $allowedDependenciesInBffApiAdapters))
        ->because('we want isolate our bffApi Adapters from ever growing dependencies.');

    $config->add($bffApiClassSet, $bffApiCoreIsolationRule, $bffApiAdaptersIsolationRule, ...$bffApiPortAndAdapterArchitectureRules);



    /**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *
     *      Metadata
     *
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     */

    $metadataClassSet = ClassSet::fromDir(__DIR__ . '/_metadata/src');

    $allowedVendorDependenciesInMetadataCore = require_once __DIR__ . '/tools/phparkitect/VendorDependencies/allowed_in_metadata_core.php';
    $allowedVendorDependenciesInMetadataAdapters = require_once __DIR__ . '/tools/phparkitect/VendorDependencies/allowed_in_metadata_adapters.php';

//    $metadataPortAndAdapterArchitectureRules = Architecture::withComponents()
//        ->component('Core')->definedBy('Metadata\Core\*')
//        ->component('Adapters')->definedBy('Metadata\Adapter*')
//        ->component('Infrastructure')->definedBy('Metadata\Infrastructure\*')
//
//        ->where('Infrastructure')->shouldNotDependOnAnyComponent()
//        ->where('Adapters')->mayDependOnComponents('Core', 'Infrastructure')
//        ->where('Core')->shouldNotDependOnAnyComponent()
//        ->rules();

    $allowedDependenciesInMetadataCore = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInMetadataCore);
    $metadataCoreIsolationRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('Metadata\Core'))
        ->should(new NotHaveDependencyOutsideNamespace('Metadata\Core', $allowedDependenciesInMetadataCore))
        ->because('we want isolate our metadata core domain from external world.');


    $allowedDependenciesInMetadataAdapters = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInMetadataAdapters);
    $metadataAdaptersIsolationRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('Metadata\Adapter*'))
        ->should(new NotHaveDependencyOutsideNamespace('Metadata\Adapter*', $allowedDependenciesInMetadataAdapters))
        ->because('we want isolate our metadata Adapters from ever growing dependencies.');

    $config->add($metadataClassSet, $metadataCoreIsolationRule, $metadataAdaptersIsolationRule);


    /**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *
     *      All Application
     *
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     */

    $allApplicationClassSet = ClassSet::fromDir(__DIR__)
        ->excludePath('*tests*')
        ->excludePath('*vendor*')
        ->excludePath('*tools*')
        ->excludePath('*var*')
    ;

    $applicationModuleArchitectureRules = Architecture::withComponents()
        ->component('BffWeb')->definedBy('BffWeb\*')
        ->component('Catalog')->definedBy('Catalog\*')
        ->where('BffWeb')->shouldNotDependOnAnyComponent()
        ->where('Catalog')->shouldNotDependOnAnyComponent()
        ->rules();

    $config->add($allApplicationClassSet, ...$applicationModuleArchitectureRules);
};
