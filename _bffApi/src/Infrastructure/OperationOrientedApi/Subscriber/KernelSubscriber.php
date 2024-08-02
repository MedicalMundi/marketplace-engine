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

namespace BffApi\Infrastructure\OperationOrientedApi\Subscriber;

use BffApi\Infrastructure\OperationOrientedApi\Exception\InvalidPayloadException;
use BffApi\Infrastructure\OperationOrientedApi\Exception\NotFoundOperationException;
use JMS\Serializer\Exception\ValidationFailedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationFailedException) {
            $errors = [];
            foreach ($exception->getConstraintViolationList() as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            $event->setResponse(new JsonResponse([
                'errors' => $errors,
            ], Response::HTTP_BAD_REQUEST));
        }

        if ($exception instanceof InvalidPayloadException) {
            $event->setResponse(new JsonResponse([
                'error' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST));
        }

        if ($exception instanceof NotFoundOperationException) {
            $event->setResponse(new JsonResponse([
                'errors' => [
                    'operation' => $exception->getMessage(),
                ],
            ]));
        }
    }
}
