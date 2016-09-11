<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

/**
 * UserController
 */
class UserController extends FOSRestController
{
    /**
     * Create a new user account
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ApiDoc(
     *     section="Users",
     *     description="Create an user",
     *     input={"class"="AppBundle\Form\UserType", "name"=""},
     *     statusCodes={
     *         201="Returned when user was created",
     *         400="Returned when parameters are invalids"
     *     },
     *     responseMap={
     *         201="AppBundle\Entity\User"
     *     }
     * )
     */
    public function postUserAction(Request $request)
    {
        $requestContent = json_decode($request->getContent(), true);

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($requestContent);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form, 400));
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $userUrl = $this->generateUrl(
            'get_user',
            ['userId' => $user->getId()],
            true
        );

        $view = $this->view($user, 201)
            ->setHeader('Location', $userUrl);
        return $this->handleView($view);
    }


    /**
     * Get an user
     *
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ApiDoc(
     *     section="Users",
     *     description="Get an user",
     *     requirements={
     *         {"name"="userId", "requirement"="\d+", "dataType"="integer", "description"="User ID"}
     *     },
     *     output="\AppBundle\Entity\User",
     *     statusCodes={
     *         200="Returned when user was found",
     *         403="Returned when user is not authorized to get an user",
     *         404="Returned when user was not found"
     *     }
     * )
     */
    public function getUserAction($userId)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy([
            'id' => $userId
        ]);

        $view = $this->view($user, 200);
        return $this->handleView($view);
    }
}
