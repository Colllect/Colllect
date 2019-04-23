<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedCachedAdapter;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedCachedStorageAdapter;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFlysystemAdapterInterface;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedLocalAdapter;
use App\Entity\User;

abstract class AbstractCachedFilesystemAdapter
{
    /**
     * @var string Cache root path
     */
    private $cacheRoot;

    /**
     * @var int Cache duration
     */
    private $cacheDuration;

    public function __construct(string $fsCacheRoot, int $fsCacheDuration)
    {
        $this->cacheRoot = rtrim($fsCacheRoot, '/') . '/'; // Ensure trailing slash
        $this->cacheDuration = $fsCacheDuration;
    }

    /**
     * Decorate an adapter with a local cached adapter.
     */
    public function cacheAdapter(
        EnhancedFlysystemAdapterInterface $adapter,
        User $user
    ): EnhancedFlysystemAdapterInterface {
        $adapter = new EnhancedCachedAdapter(
            $adapter,
            new EnhancedCachedStorageAdapter(
                new EnhancedLocalAdapter(
                    $this->cacheRoot . $this::getName()
                ),
                sha1($user->getEmail()),
                $this->cacheDuration
            )
        );

        return $adapter;
    }

    /**
     * Get filesystem adapter name.
     */
    abstract public static function getName(): string;
}
