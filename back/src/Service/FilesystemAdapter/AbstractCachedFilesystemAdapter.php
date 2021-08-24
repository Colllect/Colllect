<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFlysystemAdapterInterface;
use Exception;

abstract class AbstractCachedFilesystemAdapter
{
    /**
     * Decorate an adapter with a local cached adapter.
     *
     * @throws Exception
     */
    public function cacheAdapter(
        EnhancedFlysystemAdapterInterface $adapter,
        User $user
    ): EnhancedFlysystemAdapterInterface {
        $userEmail = $user->getEmail();

        if ($userEmail === null) {
            throw new \Exception('user_not_logged_id');
        }

        // TODO: add EnhancedCachedAdapter

        return $adapter;
    }

    /**
     * Get filesystem adapter name.
     */
    abstract public static function getName(): string;
}
