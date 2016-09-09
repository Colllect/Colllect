<?php

namespace AppBundle\Doctrine;

use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class HashPasswordListener implements EventSubscriber
{
    /**
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoder $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Pre persist listener based on doctrine common
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $user = $args->getObject();
        if (!$user instanceof User) {
            return;
        }

        $this->encodePassword($user);
    }

    /**
     * Pre update listener based on doctrine common
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $user = $args->getObject();
        if (!$user instanceof User) {
            return;
        }

        $this->encodePassword($user);

        $om = $args->getObjectManager();
        $meta = $om->getClassMetadata(get_class($user));
        if ($om instanceof EntityManager) {
            $om->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
        }
    }

    /**
     * Post update listener based on doctrine common
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args) {
        $user = $args->getObject();
        if (!$user instanceof User) {
            return;
        }

        $user->eraseCredentials();
    }

    /**
     * Post update listener based on doctrine common
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args) {
        $user = $args->getObject();
        if (!$user instanceof User) {
            return;
        }

        $user->eraseCredentials();
    }

    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate', 'postPersist', 'postUpdate'];
    }

    /**
     * @param User $user
     */
    private function encodePassword(User $user)
    {
        if (!$user->getPlainPassword()) {
            return;
        }

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($encodedPassword);
    }
}
