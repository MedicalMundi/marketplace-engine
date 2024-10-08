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

use Catalog\AdapterForGettingPublicModule\PublicModuleProviderFromPackagist;
use Catalog\Core\AntiCorruptionLayer\Dto\PackagistItemCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchModuleController extends AbstractController
{
    public function __construct(
        private readonly PublicModuleProviderFromPackagist $packagistModuleFinder
    ) {
    }

    #[Route('/search/modules', name: 'web_f_search_modules', methods: ['POST'])]
    public function index(Request $request): Response
    {
        $searchTerms = (string) $request->request->get('search');

        /** @var PackagistItemCollection $modules */
        $modules = $this->packagistModuleFinder->search($searchTerms);

        return $this->render('@web/webapi/search/module_search_result.html.twig', [
            'modules' => $modules->getItems(),
            'totalModuleCount' => $modules->count(),
        ]);
    }
}
