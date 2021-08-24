<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystem;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedSftpAdapter;

class Sftp extends AbstractCachedFilesystemAdapter implements FilesystemAdapterInterface
{
    private const NAME = 'sftp';

    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $rootPath;
    private ?EnhancedFilesystemInterface $filesystem = null;

    public function __construct(
        string $fsSftpHost,
        int $fsSftpPort,
        string $fsSftpUsername,
        string $fsSftpPassword,
        string $fsSftpRootPath
    ) {
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
                [
                    'disable_asserts' => true,
                ]
            );
        }

        return $this->filesystem;
    }
}
