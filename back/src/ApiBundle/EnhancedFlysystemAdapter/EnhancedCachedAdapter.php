<?php
namespace ApiBundle\EnhancedFlysystemAdapter;

use League\Flysystem\Cached\CachedAdapter;

class EnhancedCachedAdapter extends CachedAdapter implements EnhancedFlysystemAdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function renameDir(string $path, string $newPath): bool
    {
        $result = $this->getAdapter()->renameDir($path, $newPath);

        if ($result !== false) {
            $this->getCache()->renameDir($path, $newPath);
        }

        return $result;
    }
}
