<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\UserFilesystemCredentials;
use App\Service\FilesystemAdapter\Dropbox;
use Doctrine\ORM\EntityManagerInterface;

class UserFilesystemCredentialsService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @return string[]
     */
    public static function getSupportedUserFilesystemProviderNames(): array
    {
        return [
            Dropbox::getName(),
        ];
    }

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setUserFilesystem(User $user, string $userFilesystem, string $filesystemCredentials): void
    {
        $userFilesystemCredentials =
            $user->getFilesystemCredentials()
            ?? new UserFilesystemCredentials($user, $userFilesystem);

        $userFilesystemCredentials->setCredentials($filesystemCredentials);

        $this->entityManager->persist($userFilesystemCredentials);
        $this->entityManager->flush();
    }
}
