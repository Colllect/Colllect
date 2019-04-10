<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter;

use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;

class EnhancedFilesystem extends Filesystem implements EnhancedFilesystemInterface
{
    /**
     * Rename a directory.
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function renameDir(string $path, string $newPath): bool
    {
        return $this->rename($path, $newPath);
    }
}
