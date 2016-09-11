<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * TokenController
 */
class TokenController extends FOSRestController
{
    /**
     * Generate a new JWT
     *
     * @RequestParam(name="email", description="User email")
     * @RequestParam(name="password", description="User password")
     * @ApiDoc(
     *     section="Token",
     *     statusCodes={
     *         201="Returned when token was created",
     *         400="Returned when bad credentials"
     *     }
     * )
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postTokenAction(ParamFetcher $paramFetcher)
    {
        /** @var User $user */
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(['email' => $paramFetcher->get('email')]);

        if (!$user) {
            throw $this->createNotFoundException('No user');
        }

        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($user, $paramFetcher->get('password'));

        if (!$isValid) {
            throw new BadCredentialsException();
        }

        $token = $this->get('lexik_jwt_authentication.jwt_encoder')
            ->encode(['email' => $user->getEmail()]);

        $view = $this->view([
            'token' => $token,
        ], 201);
        return $this->handleView($view);
    }
}