<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * UserController
 */
class UserController extends FOSRestController
{
    /**
     * Create a new user account
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED)
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
     * @return User
     */
    public function postUsersAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }


    /**
     * Update an user account data
     *
     * @Rest\View()
     *
     * @ApiDoc(
     *     section="Users",
     *     requirements={
     *         {"name"="userId", "requirement"="\d+", "dataType"="integer", "description"="User ID"}
     *     },
     *     input={"class"=UserType::Class, "name"=""},
     *     statusCodes={
     *         200="Returned when user was updated",
     *         400="Returned when parameters are invalids",
     *         404="Returned when user was not found"
     *     },
     *     responseMap={
     *         200=User::Class
     *     }
     * )
     *
     * @param Request $request
     * @param int $userId
     * @return User|\Symfony\Component\Form\Form
     */
    public function putUsersAction(Request $request, $userId)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy([
            'id' => $userId
        ]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }


    /**
     * Get an user
     *
     * @Rest\View()
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
     * @return User
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

        return $user;
    }


    /**
     * Delete an user account
     *
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
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
     * @param int $userId
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
    }
}
