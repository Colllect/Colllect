<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter;

use League\Flysystem\Config;
use League\Flysystem\Local\LocalFilesystemAdapter;

class EnhancedLocalAdapter extends LocalFilesystemAdapter implements EnhancedFlysystemAdapterInterface
{
    public function renameDir(string $path, string $newPath, Config $config): void
    {
        $this->move($path, $newPath, $config);
    }
}
