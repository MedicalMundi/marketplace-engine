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

namespace BffApi\Infrastructure\OperationOrientedApi\OperationDemo;

use Symfony\Component\Validator\Constraints as Assert;

class SendPaymentInput
{
    public function __construct(
        #[Assert\NotBlank(message: 'Receiver cannot be empty')]
        public readonly string $receiver,
        #[Assert\NotBlank(message: 'Amount cannot be empty')]
        #[Assert\GreaterThan(0, message: 'Amount must be grater than 0')]
        public readonly float|int $amount,
    ) {
    }
}
