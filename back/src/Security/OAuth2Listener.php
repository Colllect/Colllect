<?php

declare(strict_types=1);

namespace App\Security;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Trikoder\Bundle\OAuth2Bundle\Security\Authentication\Token\OAuth2Token;

final class OAuth2Listener implements ListenerInterface
{
    private $tokenStorage;
    private $authenticationManager;
    private $httpMessageFactory;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager,
        HttpMessageFactoryInterface $httpMessageFactory
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->httpMessageFactory = $httpMessageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetResponseEvent $event): void
    {
        $request = $this->httpMessageFactory->createRequest($event->getRequest());
        if (!$this->isAuthorizedRequest($request)) {
            return;
        }

        try {
            /** @var OAuth2Token $authenticatedToken */
            $authenticatedToken = $this->authenticationManager->authenticate(new OAuth2Token($request, null));
        } catch (AuthenticationException $e) {
            $event->setResponse(new Response($e->getMessage(), Response::HTTP_UNAUTHORIZED));

            return;
        }
        if (!$this->isAccessToRouteGranted($event->getRequest(), $authenticatedToken)) {
            $event->setResponse(new Response('The token has insufficient scopes.', Response::HTTP_FORBIDDEN));

            return;
        }
        $this->tokenStorage->setToken($authenticatedToken);
    }

    private function isAccessToRouteGranted(Request $request, OAuth2Token $token): bool
    {
        $routeScopes = $request->attributes->get('oauth2_scopes', []);
        if (empty($routeScopes)) {
            return true;
        }

        $tokenScopes = $token
            ->getAttribute('server_request')
            ->getAttribute('oauth_scopes')
        ;

        // If the end result is empty that means that all route
        // scopes are available inside the issued token scopes.
        $scopesDiff = array_diff(
            $routeScopes,
            $tokenScopes
        );

        return empty($scopesDiff);
    }

    private function isAuthorizedRequest(ServerRequestInterface $request): bool
    {
        return $request->hasHeader('authorization')
            || \array_key_exists(CookieOrBearerTokenValidator::OAUTH_COOKIE_NAME, $request->getCookieParams());
    }
}
