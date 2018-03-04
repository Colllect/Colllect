<?php

namespace ApiBundle\FilesystemAdapter;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use ApiBundle\Entity\User;
use League\Flysystem\Plugin\ListWith;

class FilesystemAdapterManager
{
    /**
     * @var string
     */
    private $defaultFilesystemAdapterName;

    /**
     * @var FilesystemAdapterInterface[]
     */
    private $filesystemAdapters;


    public function __construct($defaultFilesystemAdapterName)
    {
        $this->defaultFilesystemAdapterName = $defaultFilesystemAdapterName;
        $this->filesystemAdapters = [];
    }


    /**
     * @param FilesystemAdapterInterface $filesystemAdapter
     * @param string $alias
     */
    public function addFilesystemAdapter(FilesystemAdapterInterface $filesystemAdapter, $alias)
    {
        $this->filesystemAdapters[$alias] = $filesystemAdapter;
    }

    /**
     * @param User $user
     * @return EnhancedFilesystemInterface
     */
    public function getFilesystem(User $user): EnhancedFilesystemInterface
    {
        $adapter = $this->filesystemAdapters[$this->defaultFilesystemAdapterName];

        if ($user->getDropboxToken() !== null) {
            $adapter = $this->filesystemAdapters['dropbox'];
        }

        $filesystem = $adapter->getFilesystem($user);
        $filesystem->addPlugin(new ListWith());

        return $filesystem;
    }
}
