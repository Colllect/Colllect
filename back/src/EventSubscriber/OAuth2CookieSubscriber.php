<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class OAuth2CookieSubscriber implements EventSubscriberInterface
{
    public const OAUTH2_COOKIE_NAME = 'colllect_oauth2';

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 255]],
            KernelEvents::RESPONSE => [['onKernelResponse', 255]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->headers->has('Authorization') || !$request->cookies->has(self::OAUTH2_COOKIE_NAME)) {
            return;
        }

        $jwt = $request->cookies->get(self::OAUTH2_COOKIE_NAME);
        $request->headers->add(
            [
                'Authorization' => 'Bearer ' . $jwt,
            ]
        );
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if ($response->getStatusCode() !== Response::HTTP_UNAUTHORIZED) {
            return;
        }

        // Clear OAuth2 cookie from response on 401
        $request = $event->getRequest();
        if ($request->cookies->has(self::OAUTH2_COOKIE_NAME)) {
            $response->headers->clearCookie(self::OAUTH2_COOKIE_NAME, '/');
        }
    }
}
