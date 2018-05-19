<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\User;
use ApiBundle\Form\Type\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * UserController
 */
class UserController extends FOSRestController
{
    /**
     * Create a new user account
     *
     * @Rest\View(statusCode=201)
     *
     * @Operation(
     *     tags={"Users"},
     *     summary="Create a new user account",
     *     @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         description="User email address",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="nickname",
     *         in="formData",
     *         description="User nickname",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="plainPassword",
     *         in="formData",
     *         description="User password",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returned when user was created",
     *         @SWG\Schema(@Model(type=User::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when parameters are invalids"
     *     )
     * )
     *
     * @param Request $request
     * @return User|Form
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
     * @Operation(
     *     tags={"Users"},
     *     summary="Update an user account data",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         description="User email address",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="nickname",
     *         in="formData",
     *         description="User nickname",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="User password",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when user was updated",
     *         @SWG\Schema(@Model(type=User::class))
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Returned when parameters are invalids"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when user was not found"
     *     )
     * )
     *
     * @param Request $request
     * @param int $userId
     * @return User|Form
     */
    public function putUsersAction(Request $request, int $userId)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('ApiBundle:User')->findOneBy([
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
     * @Operation(
     *     tags={"Users"},
     *     summary="Get an user",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when user was found",
     *         @SWG\Schema(@Model(type=User::class))
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="Returned when user is not authorized to get an user"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when user was not found"
     *     )
     * )
     *
     * @param int $userId
     *
     * @return User
     */
    public function getUserAction(int $userId)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('ApiBundle:User')->findOneBy([
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
     * @Rest\View(statusCode=204)
     *
     * @Operation(
     *     tags={"Users"},
     *     summary="Delete an user account",
     *     security={{
     *         "api_key": {}
     *     }},
     *     @SWG\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Returned when user was found and deleted"
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="Returned when user is not authorized to delete this user"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when user was not found"
     *     )
     * )
     *
     * @param int $userId
     */
    public function deleteUserAction(int $userId)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('ApiBundle:User')->findOneBy([
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
