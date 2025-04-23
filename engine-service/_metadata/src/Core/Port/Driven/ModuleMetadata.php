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

namespace Metadata\Core\Port\Driven;

use Ramsey\Uuid\UuidInterface;

class ModuleMetadata
{
    public function __construct(
        private readonly UuidInterface $moduleId,
        private readonly string $repositoryUrl,
        private string $category = 'Unknown',
        private array $tags = [],
        private bool $enableSync = true,
    ) {
    }

    public function moduleId(): UuidInterface
    {
        return $this->moduleId;
    }

    public function repositoryUrl(): string
    {
        return $this->repositoryUrl;
    }

    public function isSynchronizable(): bool
    {
        return $this->enableSync;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function changeCategory(string $category): void
    {
        $this->category = $category;
    }

    public function enableSynchronization(bool $enableSync): void
    {
        $this->enableSync = $enableSync;
    }

    public function changeTags(array $tags): void
    {
        $this->tags = $tags;
    }
}
