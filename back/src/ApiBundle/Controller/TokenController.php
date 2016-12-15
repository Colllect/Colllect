<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
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
     * @Rest\RequestParam(name="email", description="User email")
     * @Rest\RequestParam(name="password", description="User password")
     * @Rest\View(statusCode=201)
     * @ApiDoc(
     *     section="Token",
     *     statusCodes={
     *         201="Returned when token was created",
     *         400="Returned when bad credentials"
     *     }
     * )
     * @param ParamFetcher $paramFetcher
     * @return array
     */
    public function postTokenAction(ParamFetcher $paramFetcher)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $em->getRepository('ApiBundle:User')->findOneBy(['email' => $paramFetcher->get('email')]);

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

        // Update user last login date
        $user->setLastLogin(new \DateTime());
        $em->persist($user);
        $em->flush();

        return [
            'token' => $token,
        ];
    }
}
