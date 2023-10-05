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

namespace Catalog\Adapter\Packagist;

use Catalog\Application\ModuleDataReaderInterface;
use Packagist\Api\Client as PackagistClient;

class ModuleReaderOnPackagist implements ModuleDataReaderInterface
{
    public function __construct(
        private PackagistClient $apiClient
    ) {
    }

    public function search(string $searchTerm = ''): object|array
    {
        return $this->apiClient->search($searchTerm, [
            'type' => 'openemr-module',
        ]);
    }

    public function getModuleDetail(string $moduleName): object|array
    {
        return $this->apiClient->get($moduleName);
    }
}
