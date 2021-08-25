<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFilesystem\EnhancedFilesystemInterface;
use Exception;

interface FilesystemAdapterInterface
{
    /**
     * Get an initialized filesystem for a given user.
     *
     * @throws Exception
     */
    public function getFilesystem(User $user): EnhancedFilesystemInterface;
}
