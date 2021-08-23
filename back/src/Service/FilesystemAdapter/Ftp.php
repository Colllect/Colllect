<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystem;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFtpAdapter;
use League\Flysystem\Config;

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
        string $fsCacheRoot,
        int $fsCacheDuration,
        string $fsFtpHost,
        int $fsFtpPort,
        string $fsFtpUsername,
        string $fsFtpPassword,
        string $fsFtpRootPath
    ) {
        parent::__construct($fsCacheRoot, $fsCacheDuration);

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
            $adapter = $this->cacheAdapter(
                new EnhancedFtpAdapter(
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
