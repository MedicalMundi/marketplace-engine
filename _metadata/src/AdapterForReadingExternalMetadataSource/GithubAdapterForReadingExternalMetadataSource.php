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

namespace Metadata\AdapterForReadingExternalMetadataSource;

use Github\Client as GithubClient;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ExternalMetadataDto;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ForReadingExternalMetadataSource;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\MetadataReaderException;

class GithubAdapterForReadingExternalMetadataSource implements ForReadingExternalMetadataSource
{
    public function __construct(
        private readonly GithubClient $githubClient
    ) {
    }

    public function readMetadataFromExternalSource(string $moduleUrl): ExternalMetadataDto
    {
        try {
            //get composer.json
            // extract metadata section
            // create metadataDto
            return new ExternalMetadataDto(true, 'a category', ['tag-1', 'tag-2']);
        } catch (\RuntimeException $exception) {
            throw new MetadataReaderException($exception->getMessage());
        }
    }
}
