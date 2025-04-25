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

namespace BffApi\AdapterForApi;

use BffApi\Infrastructure\OperationOrientedApi\ApiInput;
use BffApi\Infrastructure\OperationOrientedApi\ApiOperationHandler;
use BffApi\Infrastructure\OperationOrientedApi\ValidationHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    public function __construct(
        private readonly ValidationHandler $validationHandler,
        private readonly ApiOperationHandler $apiOperationHandler,
    ) {
    }

    #[Route('/api/v1/operation', name: 'api_operation', methods: ['POST'])]
    public function operation(Request $request): JsonResponse
    {
        /** @var ApiInput $apiInput */
        $apiInput = $this->validationHandler->deserializeAndValidate($request->getContent(), ApiInput::class);
        $apiOutput = $this->apiOperationHandler->performOperation($apiInput);

        return new JsonResponse($apiOutput->data, $apiOutput->code);
    }
}
