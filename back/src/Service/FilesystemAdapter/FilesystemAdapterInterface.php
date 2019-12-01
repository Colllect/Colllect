<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;

interface FilesystemAdapterInterface
{
    /**
     * Get an initialized filesystem for a given user.
     */
    public function getFilesystem(User $user): EnhancedFilesystemInterface;
}
