<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Repository\UserRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * UserController
 */
class UserController extends FOSRestController
{
    /**
     * Create a new user account
     *
     * @ApiDoc(
     *     section="Users",
     *     input={"class"=UserType::Class, "name"=""},
     *     statusCodes={
     *         201="Returned when user was created",
     *         400="Returned when parameters are invalids"
     *     },
     *     responseMap={
     *         201=User::Class
     *     }
     * )
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
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
            UrlGeneratorInterface::ABSOLUTE_PATH
        );

        $view = $this->view($user, 201)
            ->setHeader('Location', $userUrl);
        return $this->handleView($view);
    }


    /**
     * Get an user
     *
     * @ApiDoc(
     *     section="Users",
     *     requirements={
     *         {"name"="userId", "requirement"="\d+", "dataType"="integer", "description"="User ID"}
     *     },
     *     output=User::Class,
     *     statusCodes={
     *         200="Returned when user was found",
     *         403="Returned when user is not authorized to get an user",
     *         404="Returned when user was not found"
     *     }
     * )
     *
     * @param $userId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUserAction($userId)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy([
            'id' => $userId
        ]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $view = $this->view($user, 200);
        return $this->handleView($view);
    }


    /**
     * Delete an user account
     *
     * @ApiDoc(
     *     section="Users",
     *     requirements={
     *         {"name"="userId", "requirement"="\d+", "dataType"="integer", "description"="User ID"}
     *     },
     *     statusCodes={
     *         204="Returned when user was found and deleted",
     *         403="Returned when user is not authorized to delete this user",
     *         404="Returned when user was not found"
     *     }
     * )
     *
     * @param $userId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteUserAction($userId)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy([
            'id' => $userId
        ]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $view = $this->view("", 204);
        return $this->handleView($view);
    }
}
