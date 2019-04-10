<?php

declare(strict_types=1);

namespace App\EnhancedFlysystemAdapter;

use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;

class EnhancedFilesystem extends Filesystem implements EnhancedFilesystemInterface
{
    /**
     * Rename a directory.
     *
     * @param string $path
     * @param string $newPath
     *
     * @return bool
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function renameDir(string $path, string $newPath): bool
    {
        return $this->rename($path, $newPath);
    }
}
