<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter;

use League\Flysystem\Config;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;

interface EnhancedFlysystemAdapterInterface extends FilesystemAdapter
{
    /**
     * Rename a directory.
     *
     * @throws FilesystemException
     */
    public function renameDir(string $path, string $newPath, Config $config): void;
}
