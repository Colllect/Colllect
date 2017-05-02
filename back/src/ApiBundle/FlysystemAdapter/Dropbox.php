<?php

namespace ApiBundle\FlysystemAdapter;

use ApiBundle\Entity\User;
use ApiBundle\Exception\DropboxAccessTokenMissingException;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Adapter;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class Dropbox implements FlysystemAdapterInterface
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;


    /**
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
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

            $client = new DropboxClient($token, $this->secret);
            $adapter = new CachedAdapter(
                new DropboxAdapter($client),
                new Adapter(new LocalAdapter(
                    FlysystemAdapters::CACHE_ROOT . 'dropbox'),
                    sha1($user->getId()),
                    FlysystemAdapters::CACHE_DURATION
                )
            );

            $this->filesystem = new Filesystem(
                $adapter, new Config(
                    [
                        'disable_asserts' => true,
                    ]
                )
            );
        }

        return $this->filesystem;
    }
}
