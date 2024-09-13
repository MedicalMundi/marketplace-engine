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

use Github\Client as GithubClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class GithubController extends AbstractController
{
    public function __construct(private readonly GithubClient $githubClient)
    {
    }

    #[Route(
        path: '/{_locale}/prova',
        name: 'app_github_prova',
        requirements: [
            '_locale' => 'en|es|it',
        ],
        defaults: [
            '_locale' => 'en',
        ],
        methods: 'GET',
    )]
    public function index(): Response
    {
        $repo =$this->githubClient->repo()->show('medicalmundi', 'oe-module-npi-registry');

        dd($repo);
        return $this->render('@web/home/index.html.twig');
    }
}
