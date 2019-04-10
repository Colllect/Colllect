<?php

declare(strict_types=1);

namespace App\EnhancedFlysystemAdapter;

use League\Flysystem\AdapterInterface;

interface EnhancedFlysystemAdapterInterface extends AdapterInterface
{
    /**
     * Rename a directory.
     */
    public function renameDir(string $path, string $newPath): bool;
}
