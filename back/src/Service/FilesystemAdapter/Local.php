<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFilesystem\EnhancedFilesystem;
use App\Service\FilesystemAdapter\EnhancedFilesystem\EnhancedFilesystemInterface;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedLocalAdapter;

class Local implements FilesystemAdapterInterface
{
    private readonly string $rootPath;

    private ?EnhancedFilesystemInterface $filesystem = null;

    public function __construct(string $fsLocalRootPath)
    {
        $this->rootPath = rtrim($fsLocalRootPath, '/') . '/'; // Ensure trailing slash
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystem(User $user): EnhancedFilesystemInterface
    {
        if ($this->filesystem === null) {
            $adapter = new EnhancedLocalAdapter(
                $this->rootPath . $user->getId(),
                null,
                \LOCK_EX,
                EnhancedLocalAdapter::SKIP_LINKS
            );

            $this->filesystem = new EnhancedFilesystem($adapter);
        }

        return $this->filesystem;
    }
}
