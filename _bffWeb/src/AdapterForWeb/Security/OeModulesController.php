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

namespace BffWeb\AdapterForWeb\Security;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OeModulesController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     */
    #[Route('/connect/oemodules', name: 'connect_oemodules_start', methods: ['GET'])]
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('oe_modules')
            ->redirect([
                'email',
            ], []);
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     */
    #[Route('/connect/oemodules/check', name: 'connect_oemodules_check')]
    public function connectCheck(ClientRegistry $clientRegistry): void
    {
        /**
         * if you want to *authenticate* the user, then
         * leave this method blank and create a Guard Authenticator
         * @see https://github.com/knpuniversity/oauth2-client-bundle/blob/master/README.md
         */

        throw new \Exception('Don\'t forget to activate custom_authenticators in security.yaml');
    }
}
