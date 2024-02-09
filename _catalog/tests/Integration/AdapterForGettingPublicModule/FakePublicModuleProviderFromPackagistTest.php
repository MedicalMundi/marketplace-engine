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

use Catalog\AdapterForGettingPublicModuleFake\FakePublicModuleProviderFromPackagist;
use Catalog\Core\AntiCorruptionLayer\Dto\PackagistItemCollection;
use PHPUnit\Framework\TestCase;

class FakePublicModuleProviderFromPackagistTest extends TestCase
{
    public function test_shouldReturnAPackagistItemCollection()
    {
        $sut = new FakePublicModuleProviderFromPackagist();

        $sut->setupPackage('a-vendor-name/a-package-name', 'a cool module', 'https://www.packagist.org/a-vendor-name/a-package-name', 'https://github.com/a-vendor-name/a-package-name', 100);

        self::assertInstanceOf(PackagistItemCollection::class, $sut->search(''));
    }

    public function test_shouldCountTheResult()
    {
        $sut = new FakePublicModuleProviderFromPackagist();

        $sut->setupPackage('a-vendor-name/a-package-name', 'a cool module', 'https://www.packagist.org/a-vendor-name/a-package-name', 'https://github.com/a-vendor-name/a-package-name', 100);

        $result = $sut->search('');

        self::assertEquals(1, $result->count());
    }
}
