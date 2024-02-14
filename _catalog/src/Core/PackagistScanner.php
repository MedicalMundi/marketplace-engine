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

namespace Catalog\Core;

use Catalog\Core\AntiCorruptionLayer\Dto\PackagistItem;
use Ecotone\Messaging\Attribute\Parameter\Payload;
use Ecotone\Modelling\Attribute\EventHandler;
use Ecotone\Modelling\EventBus;
use Psr\Log\LoggerInterface;

class PackagistScanner
{
    public function __construct(
        private readonly ModuleFinder $moduleFinder,
        private readonly EventBus $eventBus,
        private readonly LoggerInterface $logger
    ) {
    }

    public function scann(): void
    {
        $foundedModules = ($this->moduleFinder->search())->getItems();

        //remove items if they are in catalog

        /** @var PackagistItem $potentialModule */
        foreach ($foundedModules as $potentialModule) {
            $this->eventBus->publishWithRouting('catalog.newModuleWasFounded', [
                'package_name' => $potentialModule->getName(),
            ]);
        }
    }

    #[EventHandler(listenTo: "catalog.newModuleWasFounded")]
    public function notifyAddModuleToCatalog(#[Payload] array $payload): void
    {
        //dd($payload);
        $this->logger->info((string) $payload['package_name']);
    }
}
