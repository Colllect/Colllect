<?php

namespace ApiBundle\EnhancedFlysystemAdapter;

use League\Flysystem\Filesystem;

class EnhancedFilesystem extends Filesystem implements EnhancedFilesystemInterface
{
    /**
     * Rename a directory
     *
     * @param string $path
     * @param string $newPath
     *
     * @return bool
     */
    public function renameDir(string $path, string $newPath): bool
    {
        return $this->rename($path, $newPath);
    }
}
