<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystem;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedSftpAdapter;
use League\Flysystem\Config;

class Sftp extends AbstractCachedFilesystemAdapter implements FilesystemAdapterInterface
{
    private const NAME = 'sftp';

    /* @var string */
    private $host;

    /* @var int */
    private $port;

    /* @var string */
    private $username;

    /* @var string */
    private $password;

    /* @var string */
    private $rootPath;

    /* @var EnhancedFilesystemInterface */
    private $filesystem;

    public function __construct(
        string $fsCacheRoot,
        int $fsCacheDuration,
        string $fsSftpHost,
        int $fsSftpPort,
        string $fsSftpUsername,
        string $fsSftpPassword,
        string $fsSftpRootPath
    ) {
        parent::__construct($fsCacheRoot, $fsCacheDuration);

        $this->host = $fsSftpHost;
        $this->port = $fsSftpPort;
        $this->username = $fsSftpUsername;
        $this->password = $fsSftpPassword;
        $this->rootPath = $fsSftpRootPath;
    }

    /**
     * {@inheritdoc}
     */
    final public static function getName(): string
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystem(User $user): EnhancedFilesystemInterface
    {
        if ($this->filesystem === null) {
            $adapter = $this->cacheAdapter(
                new EnhancedSftpAdapter(
                    [
                        'host' => $this->host,
                        'port' => $this->port,
                        'username' => $this->username,
                        'password' => $this->password,
                        'root' => $this->rootPath,
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
