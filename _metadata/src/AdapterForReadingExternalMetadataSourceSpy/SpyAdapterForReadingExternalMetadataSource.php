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

namespace Metadata\AdapterForReadingExternalMetadataSourceSpy;

use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ExternalMetadataDto;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ForReadingExternalMetadataSource;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\MetadataReaderException;

class SpyAdapterForReadingExternalMetadataSource implements ForReadingExternalMetadataSource
{
    private int $metadataReaderErrorGenerationPercentage = 0;

    public function setPaymentErrorPercentage(int $percent): void
    {
        $this->metadataReaderErrorGenerationPercentage = $percent;
    }

    /**
     * @throws MetadataReaderException
     */
    public function readMetadataFromExternalSource(string $moduleUrl): ExternalMetadataDto
    {
    }
}
