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

namespace App\Security\OauthProvider\Exception;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class OeModulesIdentityProviderException extends IdentityProviderException
{
    /**
     * Creates client exception from response.
     *
     * @param array $data Parsed response data
     *
     * @return IdentityProviderException
     */
    public static function clientException(ResponseInterface $response, array $data)
    {
        $message = (string) ($data['message'] ?? $response->getReasonPhrase());
        return static::fromResponse(
            $response,
            $message
        );
    }

    /**
     * Creates oauth exception from response.
     *
     * @param array $data Parsed response data
     *
     * @return IdentityProviderException
     */
    public static function oauthException(ResponseInterface $response, array $data)
    {
        $errorMessage = (string) ($data['error'] ?? $response->getReasonPhrase());
        return static::fromResponse(
            $response,
            $errorMessage
        );
    }

    /**
     * Creates identity exception from response.
     *
     * @return IdentityProviderException
     */
    protected static function fromResponse(ResponseInterface $response, string $message = '')
    {
        return new static($message, $response->getStatusCode(), (string) $response->getBody());
    }
}
