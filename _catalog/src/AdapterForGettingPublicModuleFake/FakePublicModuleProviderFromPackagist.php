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

namespace Catalog\AdapterForGettingPublicModuleFake;

use Catalog\Core\AntiCorruptionLayer\Dto\PackagistItem;
use Catalog\Core\AntiCorruptionLayer\Dto\PackagistItemCollection;
use Catalog\Core\AntiCorruptionLayer\ForGettingPublicModule;

class FakePublicModuleProviderFromPackagist implements ForGettingPublicModule
{
    public function __construct(
        private array $collection = []
    ) {
    }

    public function search(string $searchTerm): PackagistItemCollection
    {
        return $this->buildPackagistItemCollection();
    }

    public function getModuleDetail(string $moduleName): object|array
    {
    }

    private function buildPackagistItemCollection(): PackagistItemCollection
    {
        return new PackagistItemCollection($this->collection);
    }

    public function setupPackage(string $name, string $description, string $url, string $repository, int $downloads): void
    {
        $this->collection[] = PackagistItem::create(
            $name,
            $description,
            $url,
            $repository,
            $downloads
        );
    }
}
