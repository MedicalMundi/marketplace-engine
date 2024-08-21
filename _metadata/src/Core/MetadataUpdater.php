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

use Metadata\Core\MetadataValidationEngine\FixedTrueMetadataValidationEngineValidation;
use Metadata\Core\MetadataValidationEngine\ForMetadataSchemaValidation;
use Metadata\Core\MetadataValidationEngine\MetadataValidationException;
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
        /** TODO: Implement a real validator engine */
        private ?ForMetadataSchemaValidation $validatorEngine = null,
    ) {
        $this->validatorEngine = $validatorEngine ?? new FixedTrueMetadataValidationEngineValidation();
    }

    public function getMetadataForModule(string $moduleId): ?ModuleMetadata
    {
        return $this->metadataStore->findByModuleId($moduleId);
    }

    /**
     * @throws UnreferencedMetadataModuleException|MetadataValidationException
     */
    public function synchronizeMetadataFor(string $moduleId): void
    {
        $moduleMetadata = $this->getMetadataForModule($moduleId);

        $moduleMetadata ?? throw new UnreferencedMetadataModuleException($moduleId);

        $targetUrl = $moduleMetadata->repositoryUrl();

        try {
            $eternalMetadata = $this->metadataReader->readMetadataFromExternalSource($targetUrl);

            $this->validateMetadataOrError((array) $eternalMetadata);

            // update metadata
            $moduleMetadata->enableSynchronization($eternalMetadata->enableSync);
            $moduleMetadata->changeCategory($eternalMetadata->category);
            $moduleMetadata->changeTags($eternalMetadata->tags);

            $this->metadataStore->update($moduleMetadata);
        } catch (MetadataReaderException) {
        }
    }

    /**
     * TODO: IMPLEMENT a Metadata Validator system
     *       Should be usable as external tool
     *       * WebUi - validate a composer.json by url
     *       * WebUi - validate a loaded composer.json as file
     *       * WebUi - validate a json text loaded in page form
     *       * Api - validate a composer.json by url
     *       * Api - validate a json structure passed as POST parameter
     *
     * @throws MetadataValidationException
     */
    private function validateMetadataOrError(array $externalMetadataDto): void
    {
        $validationResult = $this->validatorEngine->validate($externalMetadataDto);

        if (false === $validationResult) {
            throw new MetadataValidationException();
        }
    }
}
