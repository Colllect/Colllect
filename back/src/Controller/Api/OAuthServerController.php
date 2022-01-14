<?php

declare(strict_types=1);

namespace App\Controller\Api;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

final class OAuthServerController
{
    public function __construct(
        private readonly AuthorizationServer $authorizationServer,
    ) {
    }

    /**
     * Generate or refresh a token.
     *
     * @ApiDoc\Areas({"default"})
     *
     * @SWG\Tag(name="OAuth")
     * @ApiDoc\Operation(
     *     security={},
     *     consumes={"application/x-www-form-urlencoded"}
     * )
     *
     * @SWG\Parameter(
     *     name="grant_type",
     *     in="formData",
     *     description="OAuth grant type",
     *     required=true,
     *     type="string",
     *     enum={"password", "refresh_token"}
     * )
     * @SWG\Parameter(
     *     name="username",
     *     in="formData",
     *     description="Username (email)",
     *     required=false,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     description="User password",
     *     required=false,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="refresh_token",
     *     in="formData",
     *     description="Refresh token",
     *     required=false,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="client_id",
     *     in="formData",
     *     description="Client id",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="client_secret",
     *     in="formData",
     *     description="Client secret",
     *     required=false,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="scope",
     *     in="formData",
     *     description="Space separated scopes",
     *     required=false,
     *     type="string"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned when token was created",
     *     @SWG\Schema(ref="#/definitions/Token")
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returned when bad credentials"
     * )
     */
    #[Route(path: '/token', name: 'token', methods: ['POST'])]
    public function token(ServerRequestInterface $serverRequest, ResponseFactoryInterface $responseFactory): ResponseInterface
    {
        $serverResponse = $responseFactory->createResponse();
        try {
            return $this->authorizationServer->respondToAccessTokenRequest($serverRequest, $serverResponse);
        } catch (OAuthServerException $oAuthServerException) {
            return $oAuthServerException->generateHttpResponse($serverResponse);
        }
    }
}
