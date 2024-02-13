<?php declare(strict_types=1);

/*
 * This file is part of the medicalmundi/marketplace-engine
 *
 * @copyright (c) 2023 MedicalMundi
 *
 * This software consists of voluntary contributions made by many individuals
 * {@link https://github.com/medicalmundi/marketplace-engine/graphs/contributors developer} and is licensed under the MIT license.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @license https://github.com/MedicalMundi/marketplace-engine/blob/main/LICENSE MIT
 */

namespace Catalog\Tests\Integration\AdapterForGettingPublicModule;

use Catalog\AdapterForGettingPublicModule\PublicModuleProviderFromPackagist;
use Catalog\Core\AntiCorruptionLayer\Dto\PackagistItemCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(PublicModuleProviderFromPackagist::class)]
class PublicModuleProviderFromPackagistTest extends KernelTestCase
{
    private PublicModuleProviderFromPackagist $moduleProvider;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->moduleProvider = $container->get(PublicModuleProviderFromPackagist::class);
    }

    #[Test]
    public function shouldReturnAnPackagistItemCollection()
    {
        self::assertInstanceOf(PackagistItemCollection::class, $this->moduleProvider->search(''));
    }
}
