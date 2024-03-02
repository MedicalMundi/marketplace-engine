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

namespace App\Security\OauthProvider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class OeModules extends AbstractProvider
{
    #const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'id';
    #public const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'email';
    use BearerAuthorizationTrait;

    public string $domain = 'https://auth.oe-modules.com';
    // For local test
    //public string $domain = 'http://172.20.0.5:80';

    public function getBaseAuthorizationUrl(): string
    {
        return $this->domain . '/authorize';
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->domain . '/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->domain . '/api/test';
    }

    //    protected function fetchResourceOwnerDetails(AccessToken $token)
    //    {
    //        $response = parent::fetchResourceOwnerDetails($token);
    //
    //        if (empty($response['email'])) {
    //            $url = $this->getResourceOwnerDetailsUrl($token) . '/emails';
    //
    //            $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
    //
    //            $responseEmail = $this->getParsedResponse($request);
    //
    //            $response['email'] = isset($responseEmail[0]['email']) ? $responseEmail[0]['email'] : null;
    //        }
    //
    //        return $response;
    //    }

    protected function getDefaultScopes()
    {
        return ['email'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            //TODO:error MESSAGE
            throw new IdentityProviderException('xxx error', $response->getStatusCode(), $response);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $user = new OeModulesResourceOwner($response);

        return $user->setDomain($this->domain);
    }
}
