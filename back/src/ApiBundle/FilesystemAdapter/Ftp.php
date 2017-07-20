<?php

namespace ApiBundle\FilesystemAdapter;

use ApiBundle\EnhancedFlysystemAdapter\EnhancedFilesystem;
use ApiBundle\EnhancedFlysystemAdapter\EnhancedFtpAdapter;
use ApiBundle\Entity\User;
use League\Flysystem\Config;
use League\Flysystem\FilesystemInterface;

class Ftp extends AbstractFilesystemAdapter implements FilesystemAdapterInterface
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
     * {@inheritdoc}
     */
    protected function getCacheName(): string
    {
        return 'ftp';
    }

    /**
     * @param User $user
     * @return FilesystemInterface
     */
    public function getFilesystem(User $user)
    {
        if (!$this->filesystem) {
            $adapter = $this->cacheAdapter(
                new EnhancedFtpAdapter(
                    [
                        'host' => $this->host,
                        'port' => $this->port,
                        'username' => $this->username,
                        'password' => $this->password,
                        'root' => $this->root,
                    ]
                ),
                $user
            );

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
