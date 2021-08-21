<?php

declare(strict_types=1);

namespace App\Security;

use DateInterval;
use Exception;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

class CookieAccessTokenProvider
{
    private $abstractGrant;

    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        AccessTokenRepositoryInterface $accessTokenRepository,
        CryptKey $privateKey,
    ) {
        $this->abstractGrant = new class() extends AbstractGrant {
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
        };
        $this->abstractGrant->setAccessTokenRepository($accessTokenRepository);
        $this->abstractGrant->setPrivateKey($privateKey);
    }

    /**
     * @throws OAuthServerException
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function getJwtAccessToken(string $username, DateInterval $accessTokenTTL): string
    {
        $client = $this->clientRepository->getClientEntity('default');

        $accessToken = $this->abstractGrant->issueAccessToken($accessTokenTTL, $client, $username);

        return (string)$accessToken;
    }
}
