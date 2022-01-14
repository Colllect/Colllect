<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\Cache\EnhancedCacheAdapter;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFlysystemAdapterInterface;
use Exception;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use Symfony\Component\Cache\Marshaller\DeflateMarshaller;

abstract class AbstractCachedFilesystemAdapter
{
    public function __construct(
        private readonly int $fsCacheDuration,
    ) {
    }

    /**
     * Decorate an adapter with a local cached adapter.
     *
     * @throws Exception
     */
    public function cachedAdapter(
        EnhancedFlysystemAdapterInterface $adapter,
        User $user,
    ): EnhancedFlysystemAdapterInterface {
        $userId = $user->getId();

        if ($userId === null) {
            throw new Exception('user_not_logged_id');
        }

        $marshaller = new DeflateMarshaller(new DefaultMarshaller());
        $cache = new FilesystemAdapter(
            'u_' . $userId,
            $this->fsCacheDuration,
            null,
            $marshaller,
        );
        $adapter = new EnhancedCacheAdapter(
            $adapter,
            $cache,
        );

        return $adapter;
    }

    /**
     * Get filesystem adapter name.
     */
    abstract public static function getName(): string;
}
