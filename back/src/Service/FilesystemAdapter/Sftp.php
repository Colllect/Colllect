<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFilesystem\EnhancedFilesystem;
use App\Service\FilesystemAdapter\EnhancedFilesystem\EnhancedFilesystemInterface;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedSftpAdapter;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;

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
        int $fsCacheDuration,
        string $fsSftpHost,
        int $fsSftpPort,
        string $fsSftpUsername,
        string $fsSftpPassword,
        string $fsSftpRootPath
    ) {
        parent::__construct($fsCacheDuration);

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
            $adapter = $this->cachedAdapter(
                new EnhancedSftpAdapter(
                    new SftpConnectionProvider(
                        $this->host,
                        $this->username,
                        $this->password,
                        null,
                        null,
                        $this->port,
                    ),
                    $this->rootPath,
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
