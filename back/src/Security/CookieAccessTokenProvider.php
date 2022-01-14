<?php

declare(strict_types=1);

namespace App\Security;

use DateInterval;
use Exception;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class CookieAccessTokenProvider
{
    private readonly CookieGrant $cookieGrant;

    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        AccessTokenRepositoryInterface $accessTokenRepository,
        CryptKey $privateKey,
    ) {
        $this->cookieGrant = new CookieGrant();

        $this->cookieGrant->setAccessTokenRepository($accessTokenRepository);
        $this->cookieGrant->setPrivateKey($privateKey);
    }

    /**
     * @throws OAuthServerException
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws Exception
     */
    public function getJwtAccessToken(string $username, DateInterval $accessTokenTTL): string
    {
        $client = $this->clientRepository->getClientEntity('default');
        if (!$client instanceof \League\OAuth2\Server\Entities\ClientEntityInterface) {
            throw new Exception('default client must be created');
        }

        $accessToken = $this->cookieGrant->issueNewAccessToken($accessTokenTTL, $client, $username);

        return (string) $accessToken;
    }
}
