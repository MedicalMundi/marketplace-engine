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

namespace Catalog\Tests\Integration\Adapter\Packagist;

use Catalog\Adapter\Packagist\ModuleReaderOnPackagist;
use Packagist\Api\Client;
use Packagist\Api\PackageNotFoundException;
use PHPUnit\Framework\TestCase;

class ModuleReaderOnPackagistTest extends TestCase
{
    public function testModuleSearch(): void
    {
        $packagistReader = $this->getModuleReaderOnPackagist();

        $result = $packagistReader->search();

        self::assertNotEmpty($result);
    }

    public function testModuleSearchWithTerms(): void
    {
        $packagistReader = $this->getModuleReaderOnPackagist();

        $result = $packagistReader->search('fax');

        self::assertNotEmpty($result);
        self::assertCount(1, $result);
    }

    public function testModuleSearchWithNotFoundModule(): void
    {
        $packagistReader = $this->getModuleReaderOnPackagist();

        $result = $packagistReader->search('unknown-module');

        self::assertEmpty($result);
        self::assertCount(0, $result);
    }

    public function testGetModuleDetail(): void
    {
        $packagistReader = $this->getModuleReaderOnPackagist();

        $result = $packagistReader->getModuleDetail('medicalmundi/oe-module-npi-registry');

        self::assertIsObject($result);
    }

    public function testThrowPackageNotFoundException(): void
    {
        self::expectException(PackageNotFoundException::class);

        $packagistReader = $this->getModuleReaderOnPackagist();

        $packagistReader->getModuleDetail('unknown-vendor/unknown package');
    }

    private function getModuleReaderOnPackagist(): ModuleReaderOnPackagist
    {
        $packagistClient = new Client();

        return new ModuleReaderOnPackagist($packagistClient);
    }
}
