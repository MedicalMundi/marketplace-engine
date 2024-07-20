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

namespace BffWeb\AdapterForWeb\Administration\Catalog;

use Catalog\Core\PackagistScanner;
use Ecotone\Modelling\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
class CatalogController extends AbstractController
{
    #[Route('/admin/catalog', name: 'web_admin_catalog_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(QueryBus $queryBus): Response
    {
        $modules = (array) $queryBus->sendWithRouting('catalog.getModuleList');

        return $this->render('@web/administration/catalog/index.html.twig', [
            'modules' => $modules,
        ]);
    }

    #[Route('/admin/catalog/scann/public', name: 'web_admin_catalog_packagist_scanner')]
    #[IsGranted('ROLE_ADMIN')]
    public function scannPublicModules(PackagistScanner $scanner, QueryBus $queryBus): Response
    {
        $scanner->scan();

        $this->addFlash('success', 'Started packagist scanner');

        return $this->redirectToRoute('web_admin_catalog_index');
    }
}
