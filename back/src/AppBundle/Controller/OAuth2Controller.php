<?php

namespace AppBundle\Controller;

use ApiBundle\Entity\User;
use GuzzleHttp\Client as GuzzleHttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OAuth2Controller extends Controller
{
    public function dropboxAction()
    {
        $query = http_build_query(
            [
                'response_type' => 'code',
                'client_id' => $this->getParameter('dropbox_key'),
                'state' => $this->generateState(),
                'redirect_uri' => $this->generateDropboxRedirectUrl(),
            ]
        );

        return $this->redirect('https://www.dropbox.com/oauth2/authorize?' . $query);
    }

    public function dropboxCompleteAction(Request $request)
    {
        if ($request->get('state') != $this->generateState()) {
            throw new AccessDeniedException();
        }

        $client = new GuzzleHttpClient();
        $response = $client->request(
            'POST',
            'https://api.dropboxapi.com/oauth2/token',
            [
                'form_params' => [
                    'code' => $request->get('code'),
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->getParameter('dropbox_key'),
                    'client_secret' => $this->getParameter('dropbox_secret'),
                    'redirect_uri' => $this->generateDropboxRedirectUrl(),
                ],
            ]
        );
        $decodedResponse = json_decode($response->getBody(), true);

        /** @var User $user */
        $user = $this->getUser();
        $user->setDropboxToken($decodedResponse['access_token']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirect('/api/doc');
    }

    private function generateState()
    {
        $secret = $this->getParameter('secret');
        $userId = $this->getUser()->getId();
        $seed = $userId . '_' . $secret;

        $i = $userId % 50;
        do {
            $state = sha1($seed);
            $i -= 1;
        } while ($i > 0);

        return $state;
    }

    private function generateDropboxRedirectUrl()
    {
        return $this->generateUrl('app_oauth2_dropbox_complete', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}

