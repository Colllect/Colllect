<?php

declare(strict_types=1);

namespace App\Controller;

use App\Security\CookieOrBearerTokenValidator;
use App\Security\LoginFormAuthenticator;
use Exception;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    private $accessTokenRepository;
    private $security;

    public function __construct(AccessTokenRepositoryInterface $accessTokenRepository, Security $security)
    {
        $this->accessTokenRepository = $accessTokenRepository;
        $this->security = $security;
    }

    /**
     * @Route("/login", name="login", methods={"GET", "POST"})
     *
     * @throws Exception
     */
    public function login(Request $request): Response
    {
        // Redirect already logged in users
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect(LoginFormAuthenticator::HOME_PATH);
        }

        // Generate CSRF token
        $csrfToken = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        $cookie = new Cookie(
            LoginFormAuthenticator::CSRF_TOKEN_COOKIE_NAME,
            $csrfToken,
            0,
            '/login',
            null,
            true,
            true,
            false,
            Cookie::SAMESITE_STRICT
        );

        $response = new Response();
        $response->headers->setCookie($cookie);

        $error = null;
        if ($request->isMethod(Request::METHOD_POST)) {
            $error = [
                'messageKey' => 'login.bad_credentials',
                'messageData' => [],
            ];
        }

        return $this->render(
            'security/login.html.twig',
            [
                'error' => $error,
                'last_username' => '',
                'csrf_token' => $csrfToken,
            ],
            $response
        );
    }

    /**
     * @Route("/login/account", name="account", methods={"GET"})
     */
    public function account(): Response
    {
        return $this->render('security/account.html.twig');
    }

    /**
     * @Route("/logout", name="logout", methods={"POST"})
     */
    public function logout(): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect('/login');
        }

        $tokenId = $this->security->getToken()->getAttribute('server_request')->getAttribute('oauth_access_token_id');
        $this->accessTokenRepository->revokeAccessToken($tokenId);

        $response = new RedirectResponse('/login');
        $response->headers->clearCookie(CookieOrBearerTokenValidator::OAUTH_COOKIE_NAME, '/');

        return $response;
    }
}
