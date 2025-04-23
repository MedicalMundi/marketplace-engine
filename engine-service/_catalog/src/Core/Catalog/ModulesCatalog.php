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

namespace Catalog\Core\Catalog;

use Ecotone\EventSourcing\Attribute\AggregateType;
use Ecotone\EventSourcing\Attribute\Stream;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\Attribute\EventSourcingAggregate;
use Ecotone\Modelling\Attribute\EventSourcingHandler;
use Ecotone\Modelling\Attribute\Identifier;
use Ecotone\Modelling\WithAggregateVersioning;
use Ramsey\Uuid\UuidInterface;

#[EventSourcingAggregate]
#[Stream("catalog.catalog_stream")]
#[AggregateType("catalog.catalog")]
class ModulesCatalog
{
    use WithAggregateVersioning;

    #[Identifier]
    private UuidInterface $moduleId;

    private string $packageName;

    private string $description;

    private string $url;

    private string $moduleType;

    #[CommandHandler]
    public static function addPublicModule(AddPublicModule $command): array
    {
        return [
            new PublicModuleWasAdded(
                $command->id,
                $command->packageName,
                $command->description,
                $command->url,
                'public'
            ),
        ];
    }

    #[EventSourcingHandler]
    public function applyProductWasAdded(PublicModuleWasAdded $event): void
    {
        $this->moduleId = $event->id;
        $this->packageName = $event->packageName;
        $this->description = $event->description;
        $this->url = $event->url;
        $this->moduleType = $event->type;
    }

    public function id(): UuidInterface
    {
        return $this->moduleId;
    }
}
