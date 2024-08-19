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

namespace Metadata\Core;

use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ForReadingExternalMetadataSource;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\MetadataReaderException;
use Metadata\Core\Port\Driven\ForStoringMetadata;
use Metadata\Core\Port\Driven\ModuleMetadata;
use Metadata\Core\Port\Driver\ForSynchronizingMetadata\ForSynchronizingMetadata;

class MetadataUpdater implements ForSynchronizingMetadata
{
    public function __construct(
        private readonly ForStoringMetadata $metadataStore,
        private readonly ForReadingExternalMetadataSource $metadataReader,
    ) {
    }

    public function getMetadataForModule(string $moduleId): ?ModuleMetadata
    {
        return $this->metadataStore->findByModuleId($moduleId);
    }

    public function synchronizeMetadataFor(string $moduleId): void
    {
        $moduleMetadata = $this->getMetadataForModule($moduleId);

        if (null === $moduleMetadata) {
            /**
             * TODO:    Implement UnreferenceModuleIdException
             *          ModuleId and repoUrl values are owned by catalog module,
             *          UnreferenceModuleIdException allow different
             *          application execution path (retry, schedule, or ask data to catalog)
             *          when the synch process hit an unkown moduleId
             */
            throw new \RuntimeException();
        }

        $targetUrl = $moduleMetadata->repositoryUrl();

        try {
            $eternalMetadata = $this->metadataReader->readMetadataFromExternalSource($targetUrl);

            /**
             * TODO: IMPLEMENT a Metadata Validator system
             *       Should be usable as external tool
             *       * WebUi - validate a composer.json by url
             *       * WebUi - validate a loaded composer.json as file
             *       * WebUi - validate a json text loaded in page form
             *       * Api - validate a composer.json by url
             *       * Api - validate a json structure passed as POST parameter
             */

            // update metadata
            $moduleMetadata->enableSynchronization($eternalMetadata->enableSync);
            $moduleMetadata->changeCategory($eternalMetadata->category);
            $moduleMetadata->changeTags($eternalMetadata->tags);

            $this->metadataStore->update($moduleMetadata);
        } catch (MetadataReaderException $exception) {
        }
    }
}
