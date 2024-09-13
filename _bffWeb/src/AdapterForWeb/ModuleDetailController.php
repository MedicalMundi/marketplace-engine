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

namespace BffWeb\AdapterForWeb;

use Ecotone\Modelling\QueryBus;
use Packagist\Api\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[AsController]
class ModuleDetailController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus
    ) {
    }

    #[Route(
        path: '/{_locale}/module/{packageName}',
        name: 'web_module_show',
        requirements: [
            '_locale' => 'en|es|it',
            'packageName' => "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+",
        ],
        defaults: [
            '_locale' => 'en',
        ],
        methods: 'GET',
    )]
    public function index(string $packageName, Client $packagistClient): Response
    {
        $packagistData = $packagistClient->get($packageName);

        if ([] === $packagistData) {
            return $this->render('@web/module_detail/show.html.twig', [
                'module' => [],
            ]);
        }

        $versions = $packagistData->getVersions();

        $allVersionByName = array_keys($versions);

        $module = [
            'package_name' => $packagistData->getName(),
            'description' => $packagistData->getDescription(),
            'url' => 'todo',
            'tags' => [],
            'all_version' => $allVersionByName,
        ];

        return $this->render('@web/module_detail/show.html.twig', [
            'module' => $module,
        ]);
    }

    #[Route(
        path: '/{_locale}/module/{packageName}/{version}',
        name: 'web_module_show_version',
        requirements: [
            '_locale' => 'en|es|it',
            'packageName' => "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+",
            'version' => Requirement::CATCH_ALL,
        ],
        defaults: [
            '_locale' => 'en',
        ],
        methods: 'GET',
    )]
    public function version(string $packageName, string $version, Client $packagistClient): Response
    {
        $packagistData = $packagistClient->get($packageName);

        if ([] === $packagistData) {
            return $this->render('@web/module_detail/x_version_not_found.html.twig', [
                'module' => [],
            ]);
        }

        $allVersion = $packagistData->getVersions();
        $versionData = $allVersion[$version];

        return $this->render('@web/module_detail/x_version.html.twig', [
            'version' => $versionData,
        ]);
    }
}
