<?php

declare(strict_types=1);

namespace App\Security;

use DateInterval;
use Exception;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

class CookieGrant extends AbstractGrant
{
    public function issueAccessToken(
        DateInterval $accessTokenTTL,
        ClientEntityInterface $client,
        $userIdentifier,
        array $scopes = []
    ): AccessTokenEntityInterface {
        return parent::issueAccessToken($accessTokenTTL, $client, $userIdentifier, $scopes);
    }

    public function getIdentifier(): string
    {
        throw new Exception('Must not be called');
    }

    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        DateInterval $accessTokenTTL
    ): ResponseTypeInterface {
        throw new Exception('Must not be called');
    }
}
