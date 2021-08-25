<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFilesystem;

use League\Flysystem\FilesystemOperator;

interface EnhancedFilesystemInterface extends FilesystemOperator
{
    /**
     * Rename a directory.
     *
     * @param array<string, mixed> $config
     */
    public function renameDir(string $path, string $newPath, array $config = []): void;
}
