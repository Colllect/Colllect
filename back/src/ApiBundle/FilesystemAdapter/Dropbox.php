<?php

namespace ApiBundle\FilesystemAdapter;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedDropboxAdapter;
use ApiBundle\EnhancedFlysystemAdapter\EnhancedFilesystem;
use ApiBundle\Entity\User;
use ApiBundle\Exception\DropboxAccessTokenMissingException;
use League\Flysystem\Config;
use League\Flysystem\FilesystemInterface;
use Spatie\Dropbox\Client as DropboxClient;

class Dropbox extends AbstractFilesystemAdapter implements FilesystemAdapterInterface
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;


    /**
     * {@inheritdoc}
     */
    protected function getCacheName(): string
    {
        return 'dropbox';
    }

    /**
     * @param User $user
     * @return FilesystemInterface
     * @throws DropboxAccessTokenMissingException
     */
    public function getFilesystem(User $user)
    {
        if (!$this->filesystem) {
            $token = $user->getDropboxToken();

            if (!$token) {
                throw new DropboxAccessTokenMissingException("error.dropbox_not_linked");
            }

            $client = new DropboxClient($token);
            $adapter = $this->cacheAdapter(new EnhancedDropboxAdapter($client), $user);

            $this->filesystem = new EnhancedFilesystem(
                $adapter,
                new Config(
                    [
                        'disable_asserts' => true,
                    ]
                )
            );
        }

        return $this->filesystem;
    }
}
