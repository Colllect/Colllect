<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

class EnhancedFilesystem extends Filesystem implements EnhancedFilesystemInterface
{
    /**
     * Rename a directory.
     *
     * @throws FilesystemException
     */
    public function renameDir(string $path, string $newPath): void
    {
        $this->move($path, $newPath);
    }
}
