<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserFilesystemCredentials;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserFilesystemCredentials|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFilesystemCredentials|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFilesystemCredentials[]    findAll()
 * @method UserFilesystemCredentials[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFilesystemCredentialsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserFilesystemCredentials::class);
    }
}
