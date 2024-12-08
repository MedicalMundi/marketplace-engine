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

namespace Metadata\Core\MetadataValidationEngine;

class MetadataValidator implements ForMetadataSchemaValidation
{
    private const ALLOWED_CATEGORY = [
        'administration',
        'billing',
        'ePrescribing',
        'miscellaneous',
        'telecom',
        'telehealth',
        'payment',
    ];

    private const ALLOWED_TAG = [
        'fax',
        'organizer',
        'reminder',
        'scheduler',
        'sms',
        'todo',
    ];

    /**
     * @throws MetadataValidationException
     */
    public function validate(array $metadata): bool
    {
        if (! \array_key_exists('category', $metadata)) {
            throw new MetadataValidationException('Metadata key \'category\' not found');
        } else {
            $category = $metadata['category'];

            if (! \is_string($category)) {
                throw new MetadataValidationException('Metadata \'Category\' should be string type');
            }

            if (! \in_array($category, self::ALLOWED_CATEGORY)) {
                throw new MetadataValidationException('Category not allowed: ' . $category);
            }
        }

        if (! \array_key_exists('tags', $metadata)) {
            throw new MetadataValidationException('Metadata key \'tags\' not found');
        } else {
            $tags = $metadata['tags'];

            if (! \is_array($tags)) {
                throw new MetadataValidationException('Metadata \'tags\' should be array type');
            }
            /** @var string $tag */
            foreach ($tags as $tag) {
                if (! \in_array($tag, self::ALLOWED_TAG)) {
                    throw new MetadataValidationException('Tag not allowed: ' . $tag);
                }
            }
        }

        return true;
    }
}
