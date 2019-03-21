<?php

declare(strict_types=1);

namespace App\FilesystemAdapter;

use App\EnhancedFlysystemAdapter\EnhancedFilesystem;
use App\EnhancedFlysystemAdapter\EnhancedSftpAdapter;
use App\Entity\User;
use League\Flysystem\Config;
use League\Flysystem\FilesystemInterface;

class Sftp extends AbstractCachedFilesystemAdapter implements FilesystemAdapterInterface
{
    const CACHE_NAME = 'sftp';

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

    public function __construct(
        string $cacheRoot,
        int $cacheDuration,
        string $host,
        int $port,
        string $username,
        string $password,
        string $root
    ) {
        parent::__construct($cacheRoot, $cacheDuration);

        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->root = $root;
    }

    /**
     * {@inheritdoc}
     */
    final protected static function getCacheName(): string
    {
        return self::CACHE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystem(User $user): FilesystemInterface
    {
        if (!$this->filesystem) {
            $adapter = $this->cacheAdapter(
                new EnhancedSftpAdapter(
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
