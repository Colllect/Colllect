<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends FOSRestController
{
    public function postUserAction(Request $request)
    {
        $requestContent = json_decode($request->getContent(), true);

        // Change password to plainPassword
        $requestContent['plainPassword'] = $requestContent['password'];
        unset($requestContent['password']);

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
            ['user' => $user->getId()],
            true
        );

        $view = $this->view($user, 201)
            ->setHeader('Location', $userUrl);
        return $this->handleView($view);
    }

    public function getUserAction(User $user)
    {
        return $user;
    }
}
