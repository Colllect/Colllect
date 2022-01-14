<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFilesystem\EnhancedFilesystemInterface;

class FilesystemAdapterManager
{
    /** @var array<string, FilesystemAdapterInterface> */
    private array $filesystemAdapters = [];

    public function __construct(
        private readonly string $defaultFilesystemAdapterName
    ) {
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
        if ($userFilesystemCredentials !== null) {
            $userFilesystemProviderName = $userFilesystemCredentials->getFilesystemProviderName();
            $adapter = $this->filesystemAdapters[$userFilesystemProviderName];
        }

        $filesystem = $adapter->getFilesystem($user);

        return $filesystem;
    }
}
