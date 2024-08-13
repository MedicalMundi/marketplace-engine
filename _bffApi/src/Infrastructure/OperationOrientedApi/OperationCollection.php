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

namespace BffApi\Infrastructure\OperationOrientedApi;

class OperationCollection
{
    private array $operations = [];

    public function getOperation(string $operation): ApiOperation
    {
        if (! isset($this->operations[$operation])) {
            throw new NotFoundOperationException(
                \sprintf('Operation %s is not defined.', $operation)
            );
        }
        return $this->operations[$operation];
    }

    public function setOperations(array $operations): void
    {
        $this->operations = $operations;
    }
}
