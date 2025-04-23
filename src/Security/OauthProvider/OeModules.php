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

namespace App\Security\OauthProvider;

use App\Security\OauthProvider\Exception\OeModulesIdentityProviderException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class OeModules extends AbstractProvider
{
    use BearerAuthorizationTrait;

    public string $domain = 'https://auth.openemrmarketplace.com';

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
        return $this->domain . '/api/v1/auth/connect/me';
    }

    protected function fetchResourceOwnerDetails(AccessToken $token)
    {
        $response = parent::fetchResourceOwnerDetails($token);

        if (empty($response['email'])) {
            $url = $this->getResourceOwnerDetailsUrl($token);

            $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

            $responseEmail = $this->getParsedResponse($request);

            $response['email'] = $responseEmail[0]['email'] ?? null;
        }

        return $response;
    }

    protected function getDefaultScopes()
    {
        return ['email'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw OeModulesIdentityProviderException::clientException($response, $data);
        } elseif (isset($data['error'])) {
            throw OeModulesIdentityProviderException::oauthException($response, $data);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $user = new OeModulesResourceOwner($response);

        return $user->setDomain($this->domain);
    }
}
