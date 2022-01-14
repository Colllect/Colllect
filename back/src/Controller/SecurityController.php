<?php

declare(strict_types=1);

namespace App\Controller;

use App\EventSubscriber\OAuth2CookieSubscriber;
use App\Security\LoginFormAuthenticator;
use App\Service\CsrfService;
use Exception;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly AccessTokenRepositoryInterface $accessTokenRepository,
        private readonly Security $security,
        private readonly CsrfService $csrfService,
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/login', name: 'login', methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        // Redirect already logged in users
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect(LoginFormAuthenticator::HOME_PATH);
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

    #[Route(path: '/login/account', name: 'account', methods: ['GET'])]
    public function account(): Response
    {
        return $this->render('security/account.html.twig');
    }

    #[Route(path: '/logout', name: 'logout', methods: ['POST'])]
    public function logout(): Response
    {
        /** @var TokenInterface $token */
        $token = $this->security->getToken();
        $tokenId = $token->getAttribute('server_request')->getAttribute('oauth_access_token_id');
        $this->accessTokenRepository->revokeAccessToken($tokenId);
        $response = $this->redirectToRoute('app_security_login');
        $response->headers->clearCookie(OAuth2CookieSubscriber::OAUTH2_COOKIE_NAME, '/');

        return $response;
    }
}
