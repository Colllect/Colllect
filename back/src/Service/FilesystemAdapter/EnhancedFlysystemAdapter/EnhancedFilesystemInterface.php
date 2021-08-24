<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter;

use League\Flysystem\FilesystemOperator;

interface EnhancedFilesystemInterface extends FilesystemOperator
{
    /*
     * Rename a directory.
     */
    public function renameDir(string $path, string $newPath): void;
}
