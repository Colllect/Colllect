<?php

namespace ApiBundle\EnhancedFlysystemAdapter;

use League\Flysystem\Adapter\Local as LocalAdapter;

class EnhancedLocalAdapter extends LocalAdapter implements EnhancedFlysystemAdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function renameDir(string $path, string $newPath): bool
    {
        return $this->rename($path, $newPath);
    }
}
