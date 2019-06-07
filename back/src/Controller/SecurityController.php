<?php

declare(strict_types=1);

namespace App\Controller;

use App\Security\CookieOrBearerTokenValidator;
use App\Security\LoginFormAuthenticator;
use App\Service\CsrfService;
use Exception;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    private $accessTokenRepository;
    private $security;
    private $router;
    private $csrfService;

    public function __construct(AccessTokenRepositoryInterface $accessTokenRepository, Security $security, RouterInterface $router, CsrfService $csrfService)
    {
        $this->accessTokenRepository = $accessTokenRepository;
        $this->security = $security;
        $this->router = $router;
        $this->csrfService = $csrfService;
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
            return $this->redirectToRoute(LoginFormAuthenticator::HOME_ROUTE);
        }

        $csrfToken = $this->csrfService->generateCsrfToken();

        $response = $this->csrfService->createResponseWithCsrfCookie(
            LoginFormAuthenticator::CSRF_TOKEN_COOKIE_NAME,
            $csrfToken,
            'app_security_login'
        );

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
            return $this->redirectToRoute('app_security_login');
        }

        $tokenId = $this->security->getToken()->getAttribute('server_request')->getAttribute('oauth_access_token_id');
        $this->accessTokenRepository->revokeAccessToken($tokenId);

        $response = $this->redirectToRoute('app_security_login');
        $response->headers->clearCookie(CookieOrBearerTokenValidator::OAUTH_COOKIE_NAME, '/');

        return $response;
    }
}
