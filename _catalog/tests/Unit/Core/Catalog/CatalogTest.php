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

namespace Catalog\Tests\Unit\Core\Catalog;

use Catalog\Core\Catalog\AddPublicModule;
use Catalog\Core\Catalog\ModulesCatalog;
use Ecotone\Lite\EcotoneLite;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(ModulesCatalog::class)]
class CatalogTest extends TestCase
{
    private const UUID = '048a23d9-db59-4d49-87e0-36a05ee08593';

    private const PACKAGE_NAME = 'irrelevantvendor/irrelevantpackage';

    private const TYPE = 'public';

    private const DESCRIPTION = 'A module description';

    private const URL = 'https://fake.githost.com/irrelevant/irrelevant';

    #[Test]
    public function should_add_a_public_module(): void
    {
        $expectedTodoId = Uuid::fromString(self::UUID);

        /** @var ModulesCatalog $sut */
        $sut = EcotoneLite::bootstrapFlowTesting([ModulesCatalog::class])
            ->sendCommand(new AddPublicModule(
                $expectedTodoId,
                self::PACKAGE_NAME,
                self::DESCRIPTION,
                self::URL
            ))
            ->getAggregate(ModulesCatalog::class, $expectedTodoId);

        self::assertEquals($expectedTodoId, $sut->id());
    }
}
