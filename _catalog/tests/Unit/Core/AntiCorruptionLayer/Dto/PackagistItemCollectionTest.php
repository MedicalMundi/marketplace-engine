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
use Catalog\Core\AntiCorruptionLayer\Dto\PackagistItemCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PackagistItemCollection::class)]
#[UsesClass(PackagistItem::class)]
class PackagistItemCollectionTest extends TestCase
{
    private const IRRELEVANT = 'irrelevant';

    #[Test]
    public function can_be_created(): void
    {
        $collection = new PackagistItemCollection();

        self::assertInstanceOf(PackagistItemCollection::class, $collection);
    }

    #[Test]
    public function can_be_created_with_data(): void
    {
        $data = [
            PackagistItem::create(
                self::IRRELEVANT,
                self::IRRELEVANT,
                self::IRRELEVANT,
                self::IRRELEVANT,
                10
            ),
        ];

        $collection = new PackagistItemCollection($data);

        self::assertInstanceOf(PackagistItemCollection::class, $collection);
    }

    #[Test]
    public function can_count_internal_elements(): void
    {
        $data = [
            PackagistItem::create(
                self::IRRELEVANT,
                self::IRRELEVANT,
                self::IRRELEVANT,
                self::IRRELEVANT,
                10
            ),
            PackagistItem::create(
                self::IRRELEVANT,
                self::IRRELEVANT,
                self::IRRELEVANT,
                self::IRRELEVANT,
                10
            ),
        ];

        $collection = new PackagistItemCollection($data);

        self::assertEquals(2, $collection->count());
    }

    #[Test]
    public function can_return_elements_as_array(): void
    {
        $data = [
            PackagistItem::create(
                self::IRRELEVANT,
                self::IRRELEVANT,
                self::IRRELEVANT,
                self::IRRELEVANT,
                10
            ),
            PackagistItem::create(
                self::IRRELEVANT,
                self::IRRELEVANT,
                self::IRRELEVANT,
                self::IRRELEVANT,
                10
            ),
        ];
        $collection = new PackagistItemCollection($data);

        $items = $collection->getItems();

        self::assertTrue(\is_array($items));
    }
}
