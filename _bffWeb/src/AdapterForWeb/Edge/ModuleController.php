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

namespace BffWeb\AdapterForWeb\Edge;

use Ecotone\Modelling\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
class ModuleController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus
    ) {
    }

    #[Route(
        path: '/{_locale}/edge/module/{packageName}',
        name: 'web_edge_module_show',
        requirements: [
            '_locale' => 'en|es|it',
            'packageName' => "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+",
        ],
        defaults: [
            '_locale' => 'en',
        ],
        methods: 'GET',
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function index(string $packageName): Response
    {
        $module = $this->queryBus->sendWithRouting('catalog.public.getModuleByPackageName', $packageName);

        return $this->render('@web/edge/module/show.html.twig', [
            'module' => $module,
        ]);
    }
}
