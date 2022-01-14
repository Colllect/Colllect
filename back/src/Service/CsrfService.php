<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class CsrfService
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function generateCsrfToken(): string
    {
        $csrfToken = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        return $csrfToken;
    }

    public function createResponseWithCsrfCookie(string $cookieName, string $csrfToken, string $routeName): Response
    {
        $cookie = new Cookie(
            $cookieName,
            $csrfToken,
            0,
            $this->router->generate($routeName),
            null,
            true,
            true,
            false,
            Cookie::SAMESITE_STRICT
        );

        $response = new Response();
        $response->headers->setCookie($cookie);

        return $response;
    }
}
