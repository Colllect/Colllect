<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class TokenController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postTokenAction(Request $request)
    {
        $requestContent = json_decode($request->getContent(), true);

        /** @var User $user */
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(['email' => $requestContent['_email']]);

        if (!$user) {
            throw $this->createNotFoundException('No user');
        }

        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($user, $request->get('_password'));

        if (!$isValid) {
            throw new BadCredentialsException();
        }

        $token = $this->get('lexik_jwt_authentication.jwt_encoder')
            ->encode(['email' => $user->getEmail()]);

        return new JsonResponse([
            'token' => $token,
        ]);
    }
}