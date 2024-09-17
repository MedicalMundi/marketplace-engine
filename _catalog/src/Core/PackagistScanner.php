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

namespace Catalog\Core;

use Catalog\Core\AntiCorruptionLayer\Dto\PackagistItem;
use Catalog\Core\Catalog\AddPublicModule;
use Ecotone\Messaging\Attribute\Parameter\Payload;
use Ecotone\Modelling\Attribute\EventHandler;
use Ecotone\Modelling\CommandBus;
use Ecotone\Modelling\EventBus;
use Ecotone\Modelling\QueryBus;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class PackagistScanner
{
    public function __construct(
        private readonly ModuleFinder $moduleFinder,
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        private readonly EventBus $eventBus,
        private readonly LoggerInterface $logger
    ) {
    }

    public function scan(): void
    {
        $foundedModules = ($this->moduleFinder->search())->getItems();

        /** @var PackagistItem $potentialModule */
        foreach ($foundedModules as $potentialModule) {
            if ($this->isNewModule($potentialModule->getName())) {
                $this->eventBus->publishWithRouting('catalog.newPublicModuleWasFound', [
                    'package_name' => $potentialModule->getName(),
                    'description' => $potentialModule->getDescription(),
                    'url' => $potentialModule->getUrl(),
                ]);
            } else {
                $this->logger->info('Packagist scanner: skipped already registered module: ' . $potentialModule->getName());
            }
        }
    }

    #[EventHandler(listenTo: "catalog.newPublicModuleWasFound")]
    public function notifyAddModuleToCatalog(#[Payload] array $payload): void
    {
        $moduleId = Uuid::uuid4();
        $this->commandBus->send(new AddPublicModule($moduleId, (string) $payload['package_name'], (string) $payload['description'], (string) $payload['url']));
    }

    private function isNewModule(string $name): bool
    {
        $isAlreadyRegisteredInDb = $this->queryBus->sendWithRouting('getModuleByPackageName', $name);

        return (false === $isAlreadyRegisteredInDb) ? true : false;
    }
}
