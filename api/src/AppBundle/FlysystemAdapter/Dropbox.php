<?php

namespace AppBundle\FlysystemAdapter;

use AppBundle\Entity\User;
use AppBundle\Exception\DropboxAccessTokenMissingException;
use Dropbox\Client as DropboxClient;
use League\Flysystem\Config;
use League\Flysystem\Dropbox\DropboxAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

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
            $adapter = new DropboxAdapter($client);

            $this->filesystem = new Filesystem($adapter, new Config([
                'disable_asserts' => true,
            ]));
        }

        return $this->filesystem;
    }
}
