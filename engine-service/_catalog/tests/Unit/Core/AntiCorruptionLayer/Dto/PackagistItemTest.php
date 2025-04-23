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

namespace Catalog\Tests\Unit\Core\AntiCorruptionLayer\Dto;

use Catalog\Core\AntiCorruptionLayer\Dto\PackagistItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PackagistItem::class)]
class PackagistItemTest extends TestCase
{
    private const IRRELEVANT = 'irrelevant';

    #[Test]
    public function can_be_created(): void
    {
        $packagistItem = PackagistItem::create(
            self::IRRELEVANT,
            self::IRRELEVANT,
            self::IRRELEVANT,
            self::IRRELEVANT,
            100
        );

        self::assertInstanceOf(PackagistItem::class, $packagistItem);
        self::assertSame(self::IRRELEVANT, $packagistItem->getName());
        self::assertSame(self::IRRELEVANT, $packagistItem->getDescription());
        self::assertSame(self::IRRELEVANT, $packagistItem->getUrl());
        self::assertSame(self::IRRELEVANT, $packagistItem->getRepository());
        self::assertSame(100, $packagistItem->getDownloads());
    }
}
