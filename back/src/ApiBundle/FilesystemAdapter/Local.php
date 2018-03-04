<?php

namespace ApiBundle\FilesystemAdapter;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedFilesystem;
use ApiBundle\EnhancedFlysystemAdapter\EnhancedLocalAdapter;
use ApiBundle\Entity\User;
use ApiBundle\Exception\DropboxAccessTokenMissingException;
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


    /**
     * @param string $rootPath
     */
    public function __construct($rootPath)
    {
        $this->rootPath = $rootPath;
    }


    /**
     * @param User $user
     * @return FilesystemInterface
     */
    public function getFilesystem(User $user)
    {
        if (!$this->filesystem) {
            $adapter = new EnhancedLocalAdapter(
                $this->rootPath . '/' . $user->getId(),
                LOCK_EX,
                EnhancedLocalAdapter::SKIP_LINKS
            );

            $this->filesystem = new EnhancedFilesystem($adapter);
        }

        return $this->filesystem;
    }
}
