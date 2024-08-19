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

use Metadata\AdapterForReadingMetadataFromOriginalSourceSpy\SpyAdapterForReadingExternalMetadataSource;
use Metadata\AdapterForReadingMetadataFromOriginalSourceStub\StubAdapterForReadingExternalMetadataSource;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ExternalMetadataDto;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ForReadingExternalMetadataSource;
use Metadata\Core\Port\Driven\ForStoringMetadata;
use Metadata\Core\Port\Driven\ModuleMetadata;
use Metadata\Core\Port\Driver\ForConfiguringModule\ForConfiguringModule;

class ModuleConfigurator implements ForConfiguringModule
{
    public function __construct(
        private readonly ForStoringMetadata $metadataStore,
        private readonly ForReadingExternalMetadataSource $metadataReader,
    ) {
    }

    public function createMetadata(ModuleMetadata $metadata): void
    {
        if (null === $this->metadataStore->findByModuleId($metadata->moduleId()->toString())) {
            $this->metadataStore->store($metadata);
        }
    }

    public function eraseMetadata(string $moduleId): void
    {
        if (null !== $this->metadataStore->findByModuleId($moduleId)) {
            $this->metadataStore->delete($moduleId);
        }
    }

    public function setMetadataReaderErrorPercentage(int $percent): void
    {
        if ($this->metadataReader instanceof SpyAdapterForReadingExternalMetadataSource) {
            $this->metadataReader->setPaymentErrorPercentage($percent);
        }
    }

    public function setExternalMetadataDto(string $url, ExternalMetadataDto $externalMetadataDto): void
    {
        if ($this->metadataReader instanceof StubAdapterForReadingExternalMetadataSource) {
            $this->metadataReader->setExternalMetadataDto($url, $externalMetadataDto);
        }
    }
}
