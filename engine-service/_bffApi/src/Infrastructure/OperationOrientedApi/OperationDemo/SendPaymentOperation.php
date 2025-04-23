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

namespace BffApi\Infrastructure\OperationOrientedApi\OperationDemo;

use BffApi\Infrastructure\OperationOrientedApi\ApiOutput;
use BffApi\Infrastructure\OperationOrientedApi\Attribute\OperationMetadata;
use BffApi\Infrastructure\OperationOrientedApi\OperationInterface;

#[OperationMetadata(
    name: 'SendPayment',
    /** name: OperationNames::SendPayment->name, */
    input: SendPaymentInput::class
)]
class SendPaymentOperation implements OperationInterface
{
    /**
     * @param SendPaymentInput $data
     */
    public function perform(mixed $data): ApiOutput
    {
        return new ApiOutput(
            [
                'id' => '384974197',
            ],
            200
        );
    }
}
