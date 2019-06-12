<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Security\LoginFormAuthenticator;
use App\Service\CsrfService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    const CSRF_TOKEN_COOKIE_NAME = 'colllect_csrf_token_register';

    private $csrfService;

    public function __construct(CsrfService $csrfService)
    {
        $this->csrfService = $csrfService;
    }

    /**
     * @Route("/register", name="register", methods={"GET", "POST"})
     *
     * @throws Exception
     */
    public function register(Request $request): Response
    {
        // Redirect already logged in users
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect(LoginFormAuthenticator::HOME_PATH);
        }

        $csrfToken = $this->csrfService->generateCsrfToken();

        $response = $this->csrfService->createResponseWithCsrfCookie(
            self::CSRF_TOKEN_COOKIE_NAME,
            $csrfToken,
            'app_register'
        );

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('csrfToken', HiddenType::class, [
            'mapped' => false,
            'attr' => ['value' => $csrfToken],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formCsrfToken = $request->request->get('user')['csrfToken'];
            $cookieCsrfToken = $request->cookies->get(self::CSRF_TOKEN_COOKIE_NAME);

            if ($formCsrfToken === $cookieCsrfToken) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute(LoginFormAuthenticator::LOGIN_ROUTE);
            }

            $form->addError(new FormError('invalid_csrf_token'));
        }

        return $this->render(
            'register/register.html.twig',
            [
                'form' => $form->createView(),
            ],
            $response
        );
    }
}
