<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use League\Flysystem\Plugin\ListWith;

class FilesystemAdapterManager
{
    /* @var string */
    private $defaultFilesystemAdapterName;

    /* @var FilesystemAdapterInterface[] */
    private $filesystemAdapters;

    public function __construct(string $defaultFilesystemAdapterName)
    {
        $this->defaultFilesystemAdapterName = $defaultFilesystemAdapterName;
        $this->filesystemAdapters = [];
    }

    public function addFilesystemAdapter(FilesystemAdapterInterface $filesystemAdapter, string $alias): void
    {
        $this->filesystemAdapters[$alias] = $filesystemAdapter;
    }

    public function getFilesystem(User $user): EnhancedFilesystemInterface
    {
        $adapter = $this->filesystemAdapters[$this->defaultFilesystemAdapterName];

        $userFilesystemCredentials = $user->getFilesystemCredentials();
        if ($userFilesystemCredentials) {
            $userFilesystemProviderName = $userFilesystemCredentials->getFilesystemProviderName();
            $adapter = $this->filesystemAdapters[$userFilesystemProviderName];
        }

        $filesystem = $adapter->getFilesystem($user);
        $filesystem->addPlugin(new ListWith());

        return $filesystem;
    }
}
