<?php

declare(strict_types=1);

namespace App\Controller\OAuth2Clients;

use App\Entity\User;
use App\Service\FilesystemAdapter\Dropbox;
use App\Service\UserFilesystemCredentialsService;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class DropboxController extends AbstractController
{
    private const DROPBOX_AUTHORIZE_BASE_URL = 'https://www.dropbox.com/oauth2/authorize';
    private const DROPBOX_API_OAUTH2_TOKEN_URL = 'https://api.dropboxapi.com/oauth2/token';
    private const STATE_COOKIE_NAME = 'colllect_dropbox_state';

    private $router;
    private $userFilesystemCredentialsService;
    private $dropboxKey;
    private $dropboxSecret;

    public function __construct(
        RouterInterface $router,
        UserFilesystemCredentialsService $userFilesystemCredentialsService,
        string $fsDropboxKey,
        string $fsDropboxSecret
    ) {
        $this->router = $router;
        $this->userFilesystemCredentialsService = $userFilesystemCredentialsService;
        $this->dropboxKey = $fsDropboxKey;
        $this->dropboxSecret = $fsDropboxSecret;
    }

    /**
     * @Route("", name="redirect", methods={"GET"})
     *
     * @throws Exception
     */
    public function redirectToDropbox(): RedirectResponse
    {
        $state = $this->generateState();

        $query = http_build_query(
            [
                'response_type' => 'code',
                'client_id' => $this->dropboxKey,
                'state' => $state,
                'redirect_uri' => $this->generateDropboxRedirectUrl(),
            ]
        );

        $cookie = new Cookie(
            self::STATE_COOKIE_NAME,
            $state,
            time() + 3600, // one hour
            $this->router->generate('app_oauth2_clients_dropbox_redirect'),
            null,
            true,
            true,
            false,
            Cookie::SAMESITE_STRICT
        );

        $response = $this->redirect(self::DROPBOX_AUTHORIZE_BASE_URL . '?' . $query);
        $response->headers->setCookie($cookie);

        return $response;
    }

    /**
     * @Route("/complete", name="complete", methods={"GET"})
     *
     * @throws GuzzleException
     */
    public function complete(Request $request): RedirectResponse
    {
        $stateFromRequest = $request->get('state');
        $stateFromCookie = $request->cookies->get(self::STATE_COOKIE_NAME);
        if ($stateFromRequest !== $stateFromCookie) {
            throw new AccessDeniedException();
        }

        $httpClient = new GuzzleHttpClient();
        $response = $httpClient->request(
            'POST',
            self::DROPBOX_API_OAUTH2_TOKEN_URL,
            [
                'form_params' => [
                    'code' => $request->get('code'),
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->dropboxKey,
                    'client_secret' => $this->dropboxSecret,
                    'redirect_uri' => $this->generateDropboxRedirectUrl(),
                ],
            ]
        );
        $decodedResponse = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
        $accessToken = $decodedResponse['access_token'];

        /* @var $user User */
        $user = $this->getUser();

        $this->userFilesystemCredentialsService->setUserFilesystem(
            $user,
            Dropbox::getName(),
            $accessToken
        );

        return $this->redirect('/');
    }

    /**
     * Generates an uniq string used to be sure that requests from/to Dropbox are corresponding.
     *
     * @throws Exception
     */
    private function generateState(): string
    {
        $state = sha1(random_bytes(10));

        return $state;
    }

    private function generateDropboxRedirectUrl()
    {
        return $this->generateUrl('app_oauth2_clients_dropbox_complete', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
