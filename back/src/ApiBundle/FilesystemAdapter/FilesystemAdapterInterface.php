<?php

namespace ApiBundle\FilesystemAdapter;

use ApiBundle\Entity\User;
use League\Flysystem\FilesystemInterface;

interface FilesystemAdapterInterface
{
    /**
     * Get an initialized filesystem for a given user
     *
     * @param User $user
     * @return FilesystemInterface
     */
    public function getFilesystem(User $user);
}
