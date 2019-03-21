<?php

declare(strict_types=1);

namespace App\Controller;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use Zend\Diactoros\Response;

class OAuthServerController
{
    /**
     * @var AuthorizationServer
     */
    private $authorizationServer;

    public function __construct(AuthorizationServer $authorizationServer)
    {
        $this->authorizationServer = $authorizationServer;
    }

    /**
     * Generate or refresh a token.
     *
     * @Route("/token", name="token", methods={"POST"})
     *
     * @SWG\Tag(name="OAuth")
     * @ApiDoc\Operation(security={})
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
     *
     * @param ServerRequestInterface $serverRequest
     *
     * @return ResponseInterface
     */
    public function tokenAction(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $serverResponse = new Response();

        try {
            return $this->authorizationServer->respondToAccessTokenRequest($serverRequest, $serverResponse);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($serverResponse);
        }
    }
}
