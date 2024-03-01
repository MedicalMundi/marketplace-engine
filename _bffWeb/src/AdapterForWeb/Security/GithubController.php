<?php

namespace BffWeb\AdapterForWeb\Security;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GithubController extends AbstractController
{

    /**
     * Link to this controller to start the "connect" process
     */
    #[Route('/connect/github', name: 'connect_github_start', methods: ['GET'])]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // will redirect to Facebook!
        return $clientRegistry
            ->getClient('github') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                'user', 'user.email' // the scopes you want to access
            ]);
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     */
    #[Route('/connect/github/check', name: 'connect_github_check')]
    public function connectCheckAction()
    {

        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate custom_authenticators in security.yaml');
// ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)

        ///** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client */
//        $client = $clientRegistry->getClient('github');
//
//        try {
//            // the exact class depends on which provider you're using
//            ///** @var \League\OAuth2\Client\Provider\FacebookUser $user */
//            $user = $client->fetchUser();
//
//            // do something with all this new power!
//            // e.g. $name = $user->getFirstName();
//            var_dump($user); die;
//            // ...
//        } catch (IdentityProviderException $e) {
//            // something went wrong!
//            // probably you should return the reason to the user
//            var_dump($e->getMessage()); die;
//        }
    }
}