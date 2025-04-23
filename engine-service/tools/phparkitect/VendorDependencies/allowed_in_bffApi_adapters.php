<?php declare(strict_types=1);

return [
    'Symfony\Component\HttpFoundation\JsonResponse',
    'Symfony\Component\HttpFoundation\Request',
    'Symfony\Component\HttpFoundation\Response',
    'Symfony\Bundle\FrameworkBundle\Controller\AbstractController',

    'BffApi\Infrastructure\OperationOrientedApi\ValidationHandler',
    'BffApi\Infrastructure\OperationOrientedApi\ApiOperationHandler',
    'BffApi\Infrastructure\OperationOrientedApi\ApiInput',

    'Ecotone\Modelling\QueryBus',
];