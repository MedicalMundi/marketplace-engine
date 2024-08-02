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

use BffApi\Infrastructure\OperationOrientedApi\Exception\InvalidPayloadException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationHandler
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function deserializeAndValidate(array|string $payload, string $className)
    {
        try {
            $object = (\is_array($payload)) ? $this->serializer->denormalize($payload, $className) : $this->serializer->deserialize($payload, $className, 'json');
        } catch (ExceptionInterface) {
            throw new InvalidPayloadException();
        }

        $errors = $this->validator->validate($object);
        if (\count($errors) > 0) {
            throw new ValidationFailedException(null, $errors);
        }

        return $object;
    }
}
