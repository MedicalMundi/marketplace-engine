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

namespace Catalog\Adapter\Http\Web;

use Catalog\Application\ModuleDataReaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModuleDetailsController extends AbstractController
{
    public function __construct(
        private readonly ModuleDataReaderInterface $moduleDataReader
    ) {
    }

    #[Route('/module/{moduleName}', name: 'catalog_module_details', requirements: [
        'moduleName' => '[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?',
    ], methods: ['GET'])]
    public function index(string $moduleName): Response
    {
        /**
         * TODO: handle PackageNotFoundException
         */
        $module = $this->moduleDataReader->getModuleDetail($moduleName);

        return $this->render('@catalog/module/index.html.twig', [
            'module' => $module,
        ]);
    }
}
