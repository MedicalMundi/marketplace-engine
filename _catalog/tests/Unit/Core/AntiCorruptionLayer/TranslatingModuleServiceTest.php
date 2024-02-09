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

namespace Catalog\Tests\Unit\Core\AntiCorruptionLayer;

use Catalog\AdapterForGettingPublicModuleFake\FakePublicModuleProviderFromPackagist;
use Catalog\Core\AntiCorruptionLayer\ForGettingPublicModule;
use Catalog\Core\AntiCorruptionLayer\TranslatingModuleService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TranslatingModuleServiceTest extends TestCase
{
    private ForGettingPublicModule|MockObject $moduleProvider;

    protected function setUp(): void
    {
        $this->moduleProvider = new FakePublicModuleProviderFromPackagist();
    }

    public function test_shouldBeInstatiate()
    {
        $sut = new TranslatingModuleService($this->moduleProvider);
    }

    public function test_shouldfind()
    {
        $sut = new TranslatingModuleService($this->moduleProvider);

        dd($sut->search('fax'));
    }
}
