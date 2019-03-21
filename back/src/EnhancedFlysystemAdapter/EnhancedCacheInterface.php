<?php

declare(strict_types=1);

namespace App\EnhancedFlysystemAdapter;

use League\Flysystem\Cached\CacheInterface;

interface EnhancedCacheInterface extends CacheInterface
{
    /**
     * Rename a directory.
     *
     * @param string $path
     * @param string $newPath
     */
    public function renameDir(string $path, string $newPath);
}
