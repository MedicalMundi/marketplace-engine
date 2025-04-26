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

namespace Catalog\AdapterForGettingPublicModule;

use Catalog\Core\AntiCorruptionLayer\Dto\PackagistItem;
use Catalog\Core\AntiCorruptionLayer\Dto\PackagistItemCollection;
use Catalog\Core\AntiCorruptionLayer\ForGettingPublicModule;
use Packagist\Api\Client as PackagistClient;

class PublicModuleProviderFromPackagist implements ForGettingPublicModule
{
    public function __construct(
        private PackagistClient $apiClient
    ) {
    }

    public function search(string $searchTerm = ''): PackagistItemCollection
    {
        $apiResult = $this->apiClient->search($searchTerm, [
            'type' => 'openemr-module',
        ]);

        return $this->buildPackagistItemCollection($apiResult);
    }

    private function buildPackagistItemCollection(object|array $inputItem): PackagistItemCollection
    {
        $resultItems = [];
        if (\is_object($inputItem)) {
            $resultItems[] = PackagistItem::create(
                $inputItem->getName(),
                $inputItem->getDescription(),
                $inputItem->getUrl(),
                $inputItem->getRepository(),
                (int) $inputItem->getDownloads()
            );

            return new PackagistItemCollection([$resultItems]);
        } else {
            foreach ($inputItem as $item) {
                $resultItems[] = PackagistItem::create(
                    $item->getName(),
                    $item->getDescription(),
                    $item->getUrl(),
                    $item->getRepository(),
                    (int) $item->getDownloads()
                );
            }

            return new PackagistItemCollection($resultItems);
        }
    }
}
