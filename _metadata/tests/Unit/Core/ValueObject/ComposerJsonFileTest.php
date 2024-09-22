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

namespace MetadataTests\Unit\Core\ValueObject;

use Metadata\Core\ValueObject\ComposerJsonFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ComposerJsonFile::class)]
class ComposerJsonFileTest extends TestCase
{
    #[Test]
    #[DataProvider(('jsonWithoutMetadataDataprovider'))]
    #[DataProvider(('jsonWithMetadataDataprovider'))]
    public function shouldDetectIfHasMetadata(bool $expectedResult, string $json): void
    {
        $sut = ComposerJsonFile::createFromJson($json);

        self::assertSame($expectedResult, $sut->hasMetadata());
    }

    #[Test]
    public function shouldReturnMetadata(): void
    {
        $json = '{
                "extra": {
                    "openemr-module": {
                        "metadata": {
                            "oe-modules.com": {
                                "category": "miscellaneous",
                                "tags": [
                                    "todo",
                                    "organizer",
                                    "scheduler"
                                ]
                            }
                        }
                    }
                }
            }'
        ;
        $expectedResult = [
            'category' => 'miscellaneous',
            'tags' => ['todo', 'organizer', 'scheduler'],
        ];
        $sut = ComposerJsonFile::createFromJson($json);

        self::assertSame($expectedResult, $sut->getMetadata());
    }

    #[Test]
    public function shouldReturnNullWhenThereAreNotMetadata(): void
    {
        $json = '{
                "extra": {
                    "openemr-module": {
                        "metadata": {
                        }
                    }
                }
            }'
        ;

        $sut = ComposerJsonFile::createFromJson($json);

        self::assertNull($sut->getMetadata());
    }

    public static function jsonWithoutMetadataDataprovider(): iterable
    {
        return [
            [false, ''],
            [false, ' '],
            [false, '{}'],
            [false, '{"foo": "bar"}'],
        ];
    }

    public static function jsonWithMetadataDataprovider(): iterable
    {
        return [
            [true, '{
                "extra": {
                    "openemr-module": {
                        "metadata": {
                            "oe-modules.com": {
                                "category": "miscellaneous",
                                "tags": [
                                    "todo",
                                    "organizer",
                                    "scheduler"
                                ]
                            }
                        }
                    }
                }
            }'],
            [true, '{
                "extra": {
                    "openemr-module": {
                        "metadata": {
                            "oe-modules.com": {
                                "category": "miscellaneous",
                                "tags": [
                                    "todo",
                                    "organizer",
                                    "scheduler"
                                ]
                            },
                            "other-marketplace.com": {
                                "category": "miscellaneous",
                                "tags": [
                                    "todo",
                                    "organizer",
                                    "scheduler"
                                ]
                            }
                        }
                    }
                }
            }'],
        ];
    }
}
