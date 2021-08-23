<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class HashPasswordListener implements EventSubscriber
{
    public function __construct(
       private UserPasswordEncoderInterface $passwordEncoder
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();
        if (!$user instanceof User) {
            return;
        }

        $this->encodePassword($user);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();
        if (!$user instanceof User) {
            return;
        }

        $this->encodePassword($user);

        $om = $args->getObjectManager();
        $meta = $om->getClassMetadata(\get_class($user));
        if (!$om instanceof EntityManager) {
            return;
        }
        if (!$meta instanceof ClassMetadata) {
            return;
        }
        $om->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
    }

    private function encodePassword(User $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if (!$plainPassword) {
            return;
        }

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);
    }
}
