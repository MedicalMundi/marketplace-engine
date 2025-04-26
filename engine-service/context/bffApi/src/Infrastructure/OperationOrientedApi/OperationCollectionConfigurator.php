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

namespace BffApi\Infrastructure\OperationOrientedApi;

use BffApi\Infrastructure\OperationOrientedApi\Attribute\OperationMetadata;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class OperationCollectionConfigurator
{
    public function __construct(
        #[TaggedIterator(tag: 'api.operation')]
        private readonly iterable $apiOperations
    ) {
    }

    public function configure(OperationCollection $operationCollection): void
    {
        $operations = [];

        foreach ($this->apiOperations as $operation) {
            $metadata = $this->readAttribute(OperationMetadata::class, $operation);
            $operations[$metadata->name] = new ApiOperation($operation, $metadata);
        }

        $operationCollection->setOperations($operations);
    }

    /**
     * @template T
     * @param class-string<T> $atrrClass
     * @return T|null
     */
    private function readAttribute(string $atrrClass, object $object)
    {
        $reflectionClass = new \ReflectionClass($object);
        $attrs = $reflectionClass->getAttributes($atrrClass);

        if (! empty($atrrClass)) {
            $attr = reset($attrs);
            return $attr->newInstance();
        }

        throw new \RuntimeException(
            \sprintf('Operation class %s without metadata', $object::class)
        );
    }
}
