<?php

namespace ApiBundle\FlysystemAdapter;

use ApiBundle\Entity\User;

class FlysystemAdapters
{
    const CACHE_DURATION = 3600 * 24 * 3; // 3 days
    const CACHE_ROOT = '../var/flysystem_cache/';

    /**
     * @var string
     */
    private $defaultFlysystemAdapterName;

    /**
     * @var FlysystemAdapterInterface[]
     */
    private $flysystemAdapters;


    public function __construct($defaultFlysystemAdapterName)
    {
        $this->defaultFlysystemAdapterName = $defaultFlysystemAdapterName;
        $this->flysystemAdapters = [];
    }


    /**
     * @param FlysystemAdapterInterface $flysystemAdapter
     * @param string $alias
     */
    public function addFlysystemAdapter(FlysystemAdapterInterface $flysystemAdapter, $alias)
    {
        $this->flysystemAdapters[$alias] = $flysystemAdapter;
    }

    /**
     * @param User $user
     * @return FlysystemAdapterInterface
     */
    public function getFilesystem(User $user)
    {
        $adapter = $this->flysystemAdapters[$this->defaultFlysystemAdapterName];

        if ($user->getDropboxToken() !== null) {
            $adapter = $this->flysystemAdapters['dropbox'];
        }

        $filesystem = $adapter->getFilesystem($user);

        return $filesystem;
    }
}
