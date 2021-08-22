<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserFilesystemCredentials;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserFilesystemCredentials>
 */
class UserFilesystemCredentialsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFilesystemCredentials::class);
    }
}
