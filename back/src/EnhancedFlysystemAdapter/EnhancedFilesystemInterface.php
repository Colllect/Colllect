<?php

declare(strict_types=1);

namespace App\EnhancedFlysystemAdapter;

use League\Flysystem\FilesystemInterface;

/**
 * @method array listWith(array $keys = [], string $directory = '', bool $recursive = false) List with plugin adapter
 */
interface EnhancedFilesystemInterface extends FilesystemInterface
{
    /**
     * Rename a directory.
     */
    public function renameDir(string $path, string $newPath): bool;
}
