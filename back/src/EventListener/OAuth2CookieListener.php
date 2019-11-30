<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class OAuth2CookieListener implements EventSubscriberInterface
{
    const OAUTH2_COOKIE_NAME = 'colllect_oauth2';

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 255]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->headers->has('Authorization') && $request->cookies->has(self::OAUTH2_COOKIE_NAME)) {
            $jwt = $request->cookies->get(self::OAUTH2_COOKIE_NAME);
            $request->headers->add(
                [
                    'Authorization' => 'Bearer ' . $jwt,
                ]
            );
        }
    }
}
