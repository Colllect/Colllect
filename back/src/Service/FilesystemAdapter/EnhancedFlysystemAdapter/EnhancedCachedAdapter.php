<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter;

use Exception;
use League\Flysystem\Cached\CachedAdapter;

class EnhancedCachedAdapter extends CachedAdapter implements EnhancedFlysystemAdapterInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function renameDir(string $path, string $newPath): bool
    {
        $adapter = $this->getAdapter();

        if (!$adapter instanceof EnhancedFlysystemAdapterInterface) {
            throw new Exception('Adapter must implements ' . EnhancedFlysystemAdapterInterface::class);
        }

        $result = $adapter->renameDir($path, $newPath);

        if ($result) {
            $cache = $this->getCache();

            if (!$cache instanceof EnhancedFlysystemAdapterInterface) {
                throw new Exception('Cache must implements ' . EnhancedFlysystemAdapterInterface::class);
            }

            $cache->renameDir($path, $newPath);
        }

        return $result;
    }
}
