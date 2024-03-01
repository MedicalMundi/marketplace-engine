<?php

namespace App\Security\OauthProvider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class OeModules extends AbstractProvider
{
    public string $domain = 'http://localhost:8000';

    public function getBaseAuthorizationUrl()
    {
        return $this->domain . '/authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->domain . '/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->domain . '/api/test';
    }

    protected function getDefaultScopes()
    {
        // TODO: Implement getDefaultScopes() method.
        return ['email'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        // TODO: Implement checkResponse() method.
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException('xxx error', $response->getStatusCode(), $response);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        // TODO: Implement createResourceOwner() method.
    }
}