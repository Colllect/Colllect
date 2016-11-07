<?php

namespace ApiBundle\FlysystemAdapter;

use ApiBundle\Entity\User;
use League\Flysystem\FilesystemInterface;

interface FlysystemAdapterInterface
{
    /**
     * Get an initialized filesystem for a given user
     *
     * @param User $user
     * @return FilesystemInterface
     */
    public function getFilesystem(User $user);
}
