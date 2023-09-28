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

namespace App\Adapter\Http\WebApi;

use App\Application\ModuleFinder;
use App\Application\PackagistItemCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllModuleController extends AbstractController
{
    public function __construct(
        private readonly ModuleFinder $packagistModuleFinder
    ) {
    }

    #[Route('/webapi/all-modules', name: 'webapi_all_module_table')]
    public function index(): Response
    {
        /** @var PackagistItemCollection $modules */
        $modules = $this->packagistModuleFinder->searchModule();

        return $this->render('webapi/modules/all_module_table.html.twig', [
            'modules' => $modules->getItems(),
        ]);
    }
}
