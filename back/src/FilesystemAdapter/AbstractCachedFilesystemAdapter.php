<?php

declare(strict_types=1);

namespace App\FilesystemAdapter;

use App\EnhancedFlysystemAdapter\EnhancedCachedAdapter;
use App\EnhancedFlysystemAdapter\EnhancedCachedStorageAdapter;
use App\EnhancedFlysystemAdapter\EnhancedFlysystemAdapterInterface;
use App\EnhancedFlysystemAdapter\EnhancedLocalAdapter;
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

    public function __construct(string $cacheRoot, int $cacheDuration)
    {
        $this->cacheRoot = rtrim($cacheRoot, '/') . '/'; // Ensure trailing slash
        $this->cacheDuration = $cacheDuration;
    }

    /**
     * Decorate an adapter with a local cached adapter.
     *
     * @param EnhancedFlysystemAdapterInterface $adapter
     * @param User                              $user
     *
     * @return EnhancedFlysystemAdapterInterface
     */
    public function cacheAdapter(
        EnhancedFlysystemAdapterInterface $adapter,
        User $user
    ): EnhancedFlysystemAdapterInterface {
        $adapter = new EnhancedCachedAdapter(
            $adapter,
            new EnhancedCachedStorageAdapter(
                new EnhancedLocalAdapter(
                    $this->cacheRoot . self::getCacheName()
                ),
                sha1($user->getId()),
                $this->cacheDuration
            )
        );

        return $adapter;
    }

    /**
     * Get cache folder name.
     *
     * @return string
     */
    abstract protected static function getCacheName(): string;
}
