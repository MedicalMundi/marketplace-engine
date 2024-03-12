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

namespace Catalog\Infrastructure;

use Catalog\Core\Catalog\ModulesCatalog;
use Ecotone\Dbal\Configuration\DbalConfiguration;
use Ecotone\Dbal\DbalBackedMessageChannelBuilder;
use Ecotone\EventSourcing\EventSourcingConfiguration;
use Ecotone\EventSourcing\Prooph\LazyProophEventStore;
use Ecotone\Messaging\Attribute\ServiceContext;
use Ecotone\Messaging\Store\Document\DocumentStore;

class EcotoneConfiguration
{
    #[ServiceContext]
    public function getDbalConfiguration(): DbalConfiguration
    {
        return DbalConfiguration::createWithDefaults()
            ->withDoctrineORMRepositories(true);
    }

    #[ServiceContext]
    public function catalogChannel(): DbalBackedMessageChannelBuilder
    {
        return DbalBackedMessageChannelBuilder::create("catalog");
    }

    #[ServiceContext]
    public function EventSourcingPersistenceStrategy(): EventSourcingConfiguration
    {
        return EventSourcingConfiguration::createWithDefaults()
            ->withSimpleStreamPersistenceStrategy()
            //->withPersistenceStrategyFor('some_stream', LazyProophEventStore::AGGREGATE_STREAM_PERSISTENCE)
            ->withSnapshotsFor(ModulesCatalog::class, 1, DocumentStore::class)
        ;
    }
}
