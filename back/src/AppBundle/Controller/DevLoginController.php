<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is temp, cookie should be set by front
 */
class DevLoginController extends Controller
{
    public function loginAction()
    {
        $token = $this->get('lexik_jwt_authentication.jwt_encoder')
            ->encode(['id' => 1]);

        $response = new Response();
        $response->headers->setCookie(new Cookie('access_token', $token));

        return $response;
    }
}
