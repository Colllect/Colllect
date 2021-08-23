<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\UserResolveEvent;

final class UserResolveSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserProviderInterface $userProvider,
        private UserPasswordEncoderInterface $userPasswordEncoder,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return ['trikoder.oauth2.user_resolve' => 'onUserResolve'];
    }

    public function onUserResolve(UserResolveEvent $event): void
    {
        $user = $this->userProvider->loadUserByUsername($event->getUsername());

        if ($user === null) {
            return;
        }

        if (!$this->userPasswordEncoder->isPasswordValid($user, $event->getPassword())) {
            return;
        }

        $event->setUser($user);
    }
}
