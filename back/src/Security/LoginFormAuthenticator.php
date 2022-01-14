<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\EventSubscriber\OAuth2CookieSubscriber;
use App\Repository\UserRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginFormAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var string
     */
    private const REMEMBER_ME_FIELD_NAME = 'colllect_remember_me';

    /**
     * @var string
     */
    public final const LOGIN_ROUTE = 'app_security_login';

    /**
     * @var string
     */
    public final const HOME_PATH = '/';

    /**
     * @var string
     */
    public final const CSRF_TOKEN_COOKIE_NAME = 'colllect_csrf_token_authenticate';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserPasswordEncoderInterface $passwordEncoder,
        private readonly CookieAccessTokenProvider $cookieAccessTokenProvider
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        if ($request->attributes->get('_route') !== self::LOGIN_ROUTE) {
            return false;
        }

        return $request->isMethod('POST');
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe(): bool
    {
        // Custom remember me management, so... nope!
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string>
     */
    public function getCredentials(Request $request): array
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'form_csrf_token' => $request->request->get('csrf_token'),
            'cookie_csrf_token' => $request->cookies->get(self::CSRF_TOKEN_COOKIE_NAME),
        ];

        return $credentials;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        if ($credentials['form_csrf_token'] !== $credentials['cookie_csrf_token']) {
            throw new InvalidCsrfTokenException();
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $credentials['email']]);

        if (!$user instanceof User) {
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws OAuthServerException
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): Response
    {
        $response = new RedirectResponse(self::HOME_PATH);

        if ($targetPath = $request->get('_target_path')) {
            $response = new RedirectResponse($targetPath);
        }

        $expire = 0;
        $accessTokenTTL = new DateInterval('PT2H');
        if ($request->request->getBoolean(self::REMEMBER_ME_FIELD_NAME)) {
            $expire = time() + 30 * 24 * 3600; // 30 days
            $accessTokenTTL = new DateInterval('P30D');
        }

        $jwtAccessToken = $this->cookieAccessTokenProvider->getJwtAccessToken($token->getUsername(), $accessTokenTTL);

        $response->headers->clearCookie(self::CSRF_TOKEN_COOKIE_NAME, '/login');
        $response->headers->setCookie(
            new Cookie(
                OAuth2CookieSubscriber::OAUTH2_COOKIE_NAME,
                $jwtAccessToken,
                $expire,
                '/',
                null,
                true,
                true,
                false,
                Cookie::SAMESITE_STRICT
            )
        );

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $url = $this->urlGenerator->generate(
            self::LOGIN_ROUTE,
            [
                '_target_path' => $request->getRequestUri(),
            ]
        );

        return new RedirectResponse($url);
    }
}
