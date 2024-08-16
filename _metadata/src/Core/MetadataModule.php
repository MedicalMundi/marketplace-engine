<?php declare(strict_types=1);

/*
 * This file is part of the medicalmundi/marketplace-engine
 *
 * @copyright (c) 2023 MedicalMundi
 *
 * This software consists of voluntary contributions made by many individuals
 * {@link https://github.com/medicalmundi/marketplace-engine/graphs/contributors developer} and is licensed under the MIT license.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @license https://github.com/MedicalMundi/marketplace-engine/blob/main/LICENSE MIT
 */

namespace Metadata\Core;

use Metadata\Core\Port\Driven\ForStoringMetadata;
use Metadata\Core\Port\Driver\ForConfiguringModule\ForConfiguringModule;
use Metadata\Core\Port\Driver\ForSynchronizingMetadata\ForSynchronizingMetadata;

/**
 * Module
 * Offers driver ports as API.
 * Has a configurable dependency on driven ports as RI (required interface).
 */
class MetadataModule implements MetadataModuleInterface
{
    public function __construct(
        /**
         * Driven Port
         */
        private ?ForStoringMetadata $metadataStore = null,
        /**
         * Driver Port
         */
        private ?ForConfiguringModule $moduleConfigurator = null,
        private ?ForSynchronizingMetadata $metadataUpdater = null,
    ) {
    }

    public function metadataUpdater(): ForSynchronizingMetadata
    {
        if (! $this->metadataUpdater instanceof ForSynchronizingMetadata) {
            $this->metadataUpdater = new MetadataUpdater($this->metadataStore);
        }

        return $this->metadataUpdater;
    }

    public function moduleConfigurator(): ForConfiguringModule
    {
        if (! $this->moduleConfigurator instanceof ForConfiguringModule) {
            $this->moduleConfigurator = new ModuleConfigurator($this->metadataStore);
        }

        return $this->moduleConfigurator;
    }
}
