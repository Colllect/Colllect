<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Create a new user account.
     *
     * @Route("", name="create", methods={"POST"})
     *
     * @SWG\Tag(name="Users")
     * @ApiDoc\Operation(security={})
     *
     * @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="User email address",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="nickname",
     *     in="formData",
     *     description="User nickname",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="plainPassword",
     *     in="formData",
     *     description="User password",
     *     type="string"
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Returned when user was created",
     *     @SWG\Schema(ref=@ApiDoc\Model(type=User::class))
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="Returned when parameters are invalid"
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createUser(Request $request): JsonResponse
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->json($form, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json($user, Response::HTTP_CREATED);
    }

    /**
     * Get an user.
     *
     * @Route("/{userId}", name="read", methods={"GET"})
     *
     * @SWG\Tag(name="Users")
     *
     * @SWG\Parameter(
     *     name="userId",
     *     in="path",
     *     description="User ID",
     *     type="integer"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned when user was found",
     *     @SWG\Schema(ref=@ApiDoc\Model(type=User::class))
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Returned when user is not authorized to get an user"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Returned when user was not found"
     * )
     *
     * @param int $userId
     *
     * @return JsonResponse
     */
    public function readUser(int $userId): JsonResponse
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)
            ->find($userId)
        ;

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->json($user);
    }

    /**
     * Update an user account data.
     *
     * @Route("/{userId}", name="update", methods={"PUT"})
     *
     * @SWG\Tag(name="Users")
     * @ApiDoc\Operation(security={"OAuth2Password": {"superadmin"}})
     * TODO: fix security w/ Swagger UI
     *
     * @SWG\Parameter(
     *     name="userId",
     *     in="path",
     *     description="User ID",
     *     type="integer"
     * )
     *
     * @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="User email address",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="nickname",
     *     in="formData",
     *     description="User nickname",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     description="User password",
     *     type="string"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returned when user was updated",
     *     @SWG\Schema(ref=@ApiDoc\Model(type=User::class))
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returned when parameters are invalid"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Returned when user was not found"
     * )
     *
     * @param Request $request
     * @param int     $userId
     *
     * @return JsonResponse
     */
    public function updateUser(Request $request, int $userId): JsonResponse
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)
            ->find($userId)
        ;

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->json($form, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json($user);
    }

    /**
     * Delete an user account.
     *
     * @Route("/{userId}", name="delete", methods={"DELETE"})
     *
     * @SWG\Tag(name="Users")
     *
     * @SWG\Parameter(
     *     name="userId",
     *     in="path",
     *     description="User ID",
     *     type="integer"
     * )
     *
     * @SWG\Response(
     *     response=204,
     *     description="Returned when user was found and deleted"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Returned when user is not authorized to delete this user"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Returned when user was not found"
     * )
     *
     * @param int $userId
     *
     * @return Response
     */
    public function deleteUser(int $userId): Response
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)
            ->find($userId)
        ;

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
