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

namespace Metadata\Core\Port\Driver\ForConfiguringModule;

use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ExternalMetadataDto;
use Metadata\Core\Port\Driven\ModuleMetadata;

interface ForConfiguringModule
{
    public function createMetadata(ModuleMetadata $metadata): void;

    public function eraseMetadata(string $moduleId): void;

    public function setMetadataReaderErrorPercentage(int $percent): void;

    public function setExternalMetadataDto(string $url, ExternalMetadataDto $externalMetadataDto): void;
}
