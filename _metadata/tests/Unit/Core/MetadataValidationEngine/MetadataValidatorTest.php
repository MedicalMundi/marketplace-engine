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

namespace MetadataTests\Unit\Core\MetadataValidationEngine;

use Metadata\Core\MetadataValidationEngine\MetadataValidationException;
use Metadata\Core\MetadataValidationEngine\MetadataValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MetadataValidator::class)]
#[CoversClass(MetadataValidationException::class)]
class MetadataValidatorTest extends TestCase
{
    #[Test]
    public function shouldFailWhenThereIsNoCategoryKey(): void
    {
        self::expectException(MetadataValidationException::class);
        self::expectExceptionMessage('Metadata key \'category\' not found');
        $validator = new MetadataValidator();

        $validator->validate([
            'tags' => 'irrelevant',
        ]);
    }

    #[Test]
    #[DataProvider('invalidCategoryDataprovider')]
    public function shouldFailWhenCategoryIsNotValid(array $metadata): void
    {
        self::expectException(MetadataValidationException::class);
        self::expectExceptionMessage('Metadata \'Category\' should be string type');

        $validator = new MetadataValidator();
        $validator->validate($metadata);
    }

    #[Test]
    public function shouldFailWhenThereIsNoTagsKey(): void
    {
        self::expectException(MetadataValidationException::class);
        self::expectExceptionMessage('Metadata key \'tags\' not found');
        $validator = new MetadataValidator();

        $validator->validate([
            'category' => 'billing',
        ]);
    }

    #[Test]
    #[DataProvider('invalidTagsDataprovider')]
    public function shouldFailWhenTagsIsNotValid(array $metadata): void
    {
        self::expectException(MetadataValidationException::class);
        self::expectExceptionMessage('Metadata \'tags\' should be array type');

        $validator = new MetadataValidator();
        $validator->validate($metadata);
    }

    #[Test]
    public function shouldPassTheValidation(): void
    {
        $validator = new MetadataValidator();

        $result = $validator->validate([
            'category' => 'billing',
            'tags' => ['fax', 'sms'],
        ]);

        self::assertTrue($result);
    }

    #[Test]
    #[DataProvider('approvedCategoryDataprovider')]
    #[DataProvider('approvedTagsDataprovider')]
    public function shouldPassTheValidationOnlyWithApprovedCategoryAndTags(array $metadata): void
    {
        $validator = new MetadataValidator();

        $result = $validator->validate($metadata);

        self::assertTrue($result);
    }

    public static function invalidCategoryDataprovider(): array
    {
        return [
            [[
                'category' => null,
                'tags' => 'irrelevant',
            ]],
            [[
                'category' => 0,
                'tags' => 'irrelevant',
            ]],
            [[
                'category' => [],
                'tags' => 'irrelevant',
            ]],
            [[
                'category' => new \stdClass(),
                'tags' => 'irrelevant',
            ]],
        ];
    }

    public static function invalidTagsDataprovider(): array
    {
        return [
            [[
                'category' => 'billing',
                'tags' => null,
            ]],
            [[
                'category' => 'billing',
                'tags' => 0,
            ]],
            [[
                'category' => 'billing',
                'tags' => 'string',
            ]],
            [[
                'category' => 'billing',
                'tags' => new \stdClass(),
            ]],
            [[
                'category' => 'billing',
                'tags' => 'not allowed',
            ]],
        ];
    }

    public static function approvedCategoryDataprovider(): array
    {
        return [
            [[
                'category' => 'administration',
                'tags' => ['fax'],
            ]],
            [[
                'category' => 'billing',
                'tags' => ['fax'],
            ]],
            [[
                'category' => 'ePrescribing',
                'tags' => ['fax'],
            ]],
            [[
                'category' => 'miscellaneous',
                'tags' => ['fax'],
            ]],
            [[
                'category' => 'telecom',
                'tags' => ['fax'],
            ]],
            [[
                'category' => 'telehealth',
                'tags' => ['fax'],
            ]],
            [[
                'category' => 'payment',
                'tags' => ['fax'],
            ]],
        ];
    }

    public static function approvedTagsDataprovider(): array
    {
        return [
            [[
                'category' => 'miscellaneous',
                'tags' => ['fax'],
            ]],
            [[
                'category' => 'miscellaneous',
                'tags' => ['organizer'],
            ]],
            [[
                'category' => 'miscellaneous',
                'tags' => ['scheduler'],
            ]],
            [[
                'category' => 'miscellaneous',
                'tags' => ['todo'],
            ]],



        ];
    }
}
