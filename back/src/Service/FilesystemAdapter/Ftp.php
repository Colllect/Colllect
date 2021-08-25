<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFilesystem\EnhancedFilesystem;
use App\Service\FilesystemAdapter\EnhancedFilesystem\EnhancedFilesystemInterface;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;

class Ftp extends AbstractCachedFilesystemAdapter implements FilesystemAdapterInterface
{
    private const NAME = 'ftp';

    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $rootPath;
    private ?EnhancedFilesystemInterface $filesystem = null;

    public function __construct(
        int $fsCacheDuration,
        string $fsFtpHost,
        int $fsFtpPort,
        string $fsFtpUsername,
        string $fsFtpPassword,
        string $fsFtpRootPath
    ) {
        parent::__construct($fsCacheDuration);

        $this->host = $fsFtpHost;
        $this->port = $fsFtpPort;
        $this->username = $fsFtpUsername;
        $this->password = $fsFtpPassword;
        $this->rootPath = $fsFtpRootPath;
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
                new EnhancedFtpAdapter(
                    new FtpConnectionOptions(
                        $this->host,
                        $this->rootPath,
                        $this->username,
                        $this->password,
                        $this->port,
                    ),
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
