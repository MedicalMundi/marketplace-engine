<?php

namespace Catalog\Tests\Integration\AdapterForGettingPublicModule;

use Catalog\AdapterForGettingPublicModule\PublicModuleProviderFromPackagist;
use Catalog\AdapterForGettingPublicModuleFake\FakePublicModuleProviderFromPackagist;
use Catalog\Core\AntiCorruptionLayer\Dto\PackagistItemCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PublicModuleProviderFromPackagistTest extends KernelTestCase
{
    private PublicModuleProviderFromPackagist $moduleProvider;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->moduleProvider = $container->get(PublicModuleProviderFromPackagist::class);
    }

    public function testName(): void
    {
        self::markTestSkipped();
        self::bootKernel();
        $container = static::getContainer();

        $sut = $container->get(PublicModuleProviderFromPackagist::class);

        dd($sut->search());

    }

    public function test_shouldReturnAnPackagistItemCollection()
    {
        self::assertInstanceOf(PackagistItemCollection::class, $this->moduleProvider->search(''));
    }
}