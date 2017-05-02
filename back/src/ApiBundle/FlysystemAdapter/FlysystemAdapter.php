<?php

namespace ApiBundle\FlysystemAdapter;

use ApiBundle\Entity\User;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Adapter;

abstract class FlysystemAdapter
{
    const CACHE_DURATION = 3600 * 24 * 3; // 3 days
    const CACHE_ROOT = '../var/flysystem_cache/';

    /**
     * Decorate an adapter with a local cached adapter
     *
     * @param AdapterInterface $adapter
     * @param User $user
     * @return AdapterInterface|CachedAdapter
     */
    public function cacheAdapter(AdapterInterface $adapter, User $user)
    {
        $adapter = new CachedAdapter(
            $adapter,
            new Adapter(new LocalAdapter(
                self::CACHE_ROOT . strtolower((new \ReflectionClass($this))->getShortName())),
                sha1($user->getId()),
                self::CACHE_DURATION
            )
        );

        return $adapter;
    }
}
