<?php

namespace ApiBundle\EnhancedFlysystemAdapter;

use League\Flysystem\Adapter\Ftp as FtpAdapter;

class EnhancedFtpAdapter extends FtpAdapter implements EnhancedFlysystemAdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function renameDir(string $path, string $newPath): bool
    {
        return $this->rename($path, $newPath);
    }
}
