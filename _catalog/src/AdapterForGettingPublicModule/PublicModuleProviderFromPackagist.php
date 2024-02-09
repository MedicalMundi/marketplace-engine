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

    public function search(string $searchTerm = ''): object|array
    {
        $apiResult = $this->apiClient->search($searchTerm, [
            'type' => 'openemr-module',
        ]);

        return $this->buildPackagistItemCollection($apiResult);
    }

    public function getModuleDetail(string $moduleName): object|array
    {
        return $this->apiClient->get($moduleName);
    }

    private function buildPackagistItemCollection(object|array $inputItem): PackagistItemCollection
    {
        if (is_object($inputItem)){
            $packagistItem =  PackagistItem::create(
                $inputItem->getName(),
                $inputItem->getDescription(),
                $inputItem->getUrl(),
                $inputItem->getRepository(),
                $inputItem->getDownloads()
            );

            return  new PackagistItemCollection([$packagistItem]);
        }else{
            $x = [];
            foreach ($inputItem as $item){
                $x[] = PackagistItem::create(
                    $item->getName(),
                    $item->getDescription(),
                    $item->getUrl(),
                    $item->getRepository(),
                    $item->getDownloads()
                );
            }

            return new PackagistItemCollection([$x]);
        }

    }

}
