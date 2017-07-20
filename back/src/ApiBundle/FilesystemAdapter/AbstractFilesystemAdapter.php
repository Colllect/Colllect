<?php

namespace ApiBundle\FilesystemAdapter;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedCachedAdapter;
use ApiBundle\EnhancedFlysystemAdapter\EnhancedCachedStorageAdapter;
use ApiBundle\EnhancedFlysystemAdapter\EnhancedFlysystemAdapterInterface;
use ApiBundle\EnhancedFlysystemAdapter\EnhancedLocalAdapter;
use ApiBundle\Entity\User;

abstract class AbstractFilesystemAdapter
{
    const CACHE_DURATION = 3600 * 24 * 3; // 3 days
    const CACHE_ROOT = '../var/flysystem_cache/';

    /**
     * Decorate an adapter with a local cached adapter
     *
     * @param EnhancedFlysystemAdapterInterface $adapter
     * @param User $user
     * @return EnhancedFlysystemAdapterInterface
     */
    public function cacheAdapter(EnhancedFlysystemAdapterInterface $adapter, User $user): EnhancedFlysystemAdapterInterface
    {
        $adapter = new EnhancedCachedAdapter(
            $adapter,
            new EnhancedCachedStorageAdapter(
                new EnhancedLocalAdapter(
                    self::CACHE_ROOT . $this->getCacheName()
                ),
                sha1($user->getId()),
                self::CACHE_DURATION
            )
        );

        return $adapter;
    }

    /**
     * Get cache folder name
     *
     * @return string
     */
    abstract protected function getCacheName(): string;
}
