<?php

declare(strict_types=1);

namespace App\Security;

use DateInterval;
use Exception;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

class CookieGrant extends AbstractGrant
{
    /**
     * @param ScopeEntityInterface[] $scopes
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     * @throws \League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException
     */
    public function issueNewAccessToken(
        DateInterval $accessTokenTTL,
        ClientEntityInterface $client,
        string|null $userIdentifier,
        array $scopes = []
    ): AccessTokenEntityInterface {
        return parent::issueAccessToken($accessTokenTTL, $client, $userIdentifier, $scopes);
    }

    /**
     * @throws Exception
     */
    public function getIdentifier(): string
    {
        throw new Exception('Must not be called');
    }

    /**
     * @throws Exception
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        DateInterval $accessTokenTTL
    ): ResponseTypeInterface {
        throw new Exception('Must not be called');
    }
}
