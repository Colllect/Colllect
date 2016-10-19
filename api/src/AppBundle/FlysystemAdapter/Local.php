<?php

namespace AppBundle\FlysystemAdapter;

use AppBundle\Entity\User;
use AppBundle\Exception\DropboxAccessTokenMissingException;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class Local implements FlysystemAdapterInterface
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
     * @throws DropboxAccessTokenMissingException
     */
    public function getFilesystem(User $user)
    {
        if (!$this->filesystem) {
            $adapter = new LocalAdapter($this->rootPath.'/'.$user->getId(), LOCK_EX, LocalAdapter::SKIP_LINKS);

            $this->filesystem = new Filesystem($adapter);
        }

        return $this->filesystem;
    }
}
