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

namespace Metadata\AdapterForStoringMetadataFake;

use Metadata\Core\Port\Driven\ForStoringMetadata;
use Metadata\Core\Port\Driven\ModuleMetadata;

class FakeForStoringMetadata implements ForStoringMetadata
{
    public function __construct(
        private array $modulesById = [],
    ) {
    }

    public function store(ModuleMetadata $metadata): void
    {
        if ($this->exists($metadata->moduleId()->toString())) {
            throw new \RuntimeException("Cannot store metadata. ModuleId '" . $metadata->moduleId() . "' already exists.");
        }
        $this->modulesById[$metadata->moduleId()->toString()] = $metadata;
    }

    public function update(ModuleMetadata $metadata): void
    {
        if (! $this->exists($metadata->moduleId()->toString())) {
            throw new \RuntimeException("Cannot update metadata. ModuleId '" . $metadata->moduleId() . "' not exists.");
        }
        $this->modulesById[$metadata->moduleId()->toString()] = $metadata;
    }

    public function findByModuleId(string $moduleId): ?ModuleMetadata
    {
        if (! $this->exists($moduleId)) {
            return null;
        }

        return $this->modulesById[$moduleId];
    }

    public function delete(string $moduleId): void
    {
        if (! $this->exists($moduleId)) {
            throw new \RuntimeException("Cannot delete metadata. ModuleId '" . $moduleId . "' does not exist.");
        }
        $this->modulesById[$moduleId] = null;
    }

    private function exists(string $moduleId): bool
    {
        $result = false;
        /** @var ModuleMetadata $metadata */
        foreach ($this->modulesById as $metadata) {
            if ($metadata->moduleId()->toString() === $moduleId) {
                $result = true;
            }
        }

        return $result;
    }
}
