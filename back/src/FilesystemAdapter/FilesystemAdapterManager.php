<?php

declare(strict_types=1);

namespace App\FilesystemAdapter;

use App\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use App\Entity\User;
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

    public function addFilesystemAdapter(FilesystemAdapterInterface $filesystemAdapter, string $alias): void
    {
        $this->filesystemAdapters[$alias] = $filesystemAdapter;
    }

    /**
     * @param User $user
     *
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
