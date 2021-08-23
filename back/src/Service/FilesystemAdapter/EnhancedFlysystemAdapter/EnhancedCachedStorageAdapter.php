<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter;

use League\Flysystem\Cached\Storage\Adapter;
use League\Flysystem\Util;

class EnhancedCachedStorageAdapter extends Adapter implements EnhancedCacheInterface
{
    /**
     * {@inheritdoc}
     */
    public function renameDir(string $path, string $newPath): bool
    {
        foreach ($this->cache as $object) {
            if ($object['dirname'] === $path || $this->pathIsInDirectory($path, $object['path'])) {
                unset($this->cache[$object['path']]);

                $objectNewPath = $newPath . substr($object['path'], \strlen($path));
                $object['path'] = $objectNewPath;
                $object = array_merge($object, Util::pathinfo($objectNewPath));
                $this->cache[$objectNewPath] = $object;
                $this->autosave();
            }
        }

        return true;
    }
}
