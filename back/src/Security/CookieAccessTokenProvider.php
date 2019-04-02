<?php

declare(strict_types=1);

namespace App\Security;

use DateInterval;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

class CookieAccessTokenProvider
{
    private $abstractGrant;
    private $clientRepository;
    private $privateKey;

    public function __construct(
        AccessTokenRepositoryInterface $accessTokenRepository,
        ClientRepositoryInterface $clientRepository,
        CryptKey $privateKey
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

            public function getIdentifier(): void
            {
                throw new \Exception('Must not be called');
            }

            public function respondToAccessTokenRequest(
                ServerRequestInterface $request,
                ResponseTypeInterface $responseType,
                DateInterval $accessTokenTTL
            ): void {
                throw new \Exception('Must not be called');
            }
        };
        $this->abstractGrant->setAccessTokenRepository($accessTokenRepository);

        $this->clientRepository = $clientRepository;
        $this->privateKey = $privateKey;
    }

    /**
     * @param string       $username
     * @param DateInterval $accessTokenTTL
     *
     * @return string
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     * @throws \League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException
     */
    public function getJwtAccessToken(string $username, DateInterval $accessTokenTTL): string
    {
        $client = $this->clientRepository->getClientEntity('default');

        $accessToken = $this->abstractGrant
            ->issueAccessToken($accessTokenTTL, $client, $username)
            ->convertToJWT($this->privateKey)
        ;

        return (string) $accessToken;
    }
}
