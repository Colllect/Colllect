<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFilesystem;

use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFlysystemAdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\PathNormalizer;

class EnhancedFilesystem extends Filesystem implements EnhancedFilesystemInterface
{
    protected EnhancedFlysystemAdapterInterface $enhancedAdapter;

    protected Config $enhancedConfig;

    /**
     * @param array<string, bool> $config
     */
    public function __construct(
        EnhancedFlysystemAdapterInterface $adapter,
        array $config = [],
        PathNormalizer $pathNormalizer = null
    ) {
        parent::__construct($adapter, $config, $pathNormalizer);
        $this->enhancedAdapter = $adapter;
        $this->enhancedConfig = new Config($config);
    }

    /**
     * Rename a directory.
     *
     * @throws FilesystemException
     */
    public function renameDir(string $path, string $newPath, array $config = []): void
    {
        $this->enhancedAdapter->renameDir(
            $path,
            $newPath,
            $this->enhancedConfig->extend($config)
        );
    }
}
