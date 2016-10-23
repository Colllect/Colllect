<?php

namespace AppBundle\FlysystemAdapter;

use AppBundle\Entity\User;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Adapter\Ftp as FtpAdapter;

class Ftp implements FlysystemAdapterInterface
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $root;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;


    /**
     * @param string $host
     * @param int $port
     * @param string $username
     * @param string $password
     * @param string $root
     */
    public function __construct($host, $port, $username, $password, $root)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->root = $root;
    }


    /**
     * @param User $user
     * @return FilesystemInterface
     */
    public function getFilesystem(User $user)
    {
        if (!$this->filesystem) {
            $adapter = new FtpAdapter(
                [
                    'host' => $this->host,
                    'port' => $this->port,
                    'username' => $this->username,
                    'password' => $this->password,
                    'root' => $this->root,
                ]
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
