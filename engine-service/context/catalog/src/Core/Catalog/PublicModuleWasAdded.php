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

use Ramsey\Uuid\UuidInterface;

class PublicModuleWasAdded
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $packageName,
        public readonly string $description,
        public readonly string $url,
        public readonly string $type = 'public'
    ) {
    }
}
