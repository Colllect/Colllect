<?php

declare(strict_types=1);

namespace App\FilesystemAdapter;

use App\EnhancedFlysystemAdapter\EnhancedFilesystem;
use App\EnhancedFlysystemAdapter\EnhancedLocalAdapter;
use App\Entity\User;
use League\Flysystem\FilesystemInterface;

class Local implements FilesystemAdapterInterface
{
    /**
     * @var string
     */
    private $rootPath;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    public function __construct(string $rootPath)
    {
        $this->rootPath = rtrim($rootPath, '/') . '/'; // Ensure trailing slash
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystem(User $user): FilesystemInterface
    {
        if (!$this->filesystem) {
            $adapter = new EnhancedLocalAdapter(
                $this->rootPath . $user->getId(),
                LOCK_EX,
                EnhancedLocalAdapter::SKIP_LINKS
            );

            $this->filesystem = new EnhancedFilesystem($adapter);
        }

        return $this->filesystem;
    }
}
