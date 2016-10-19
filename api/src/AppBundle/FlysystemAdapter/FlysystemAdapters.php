<?php

namespace AppBundle\FlysystemAdapter;

use AppBundle\Entity\User;

class FlysystemAdapters
{
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
