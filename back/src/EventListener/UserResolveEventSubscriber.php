<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\UserResolveEvent;

final class UserResolveEventSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    public function __construct(private UserProviderInterface $userProvider, private UserPasswordEncoderInterface $userPasswordEncoder)
    {
    }

    public function onUserResolve(UserResolveEvent $event): void
    {
        $user = $this->userProvider->loadUserByUsername($event->getUsername());
        if (!$this->userPasswordEncoder->isPasswordValid($user, $event->getPassword())) {
            return;
        }
        $event->setUser($user);
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return ['trikoder.oauth2.user_resolve' => 'onUserResolve'];
    }
}