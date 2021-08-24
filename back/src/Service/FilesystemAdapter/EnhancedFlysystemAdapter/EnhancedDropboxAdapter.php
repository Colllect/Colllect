<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter;

use League\Flysystem\Config;
use Spatie\FlysystemDropbox\DropboxAdapter;

class EnhancedDropboxAdapter extends DropboxAdapter implements EnhancedFlysystemAdapterInterface
{
    public function renameDir(string $path, string $newPath, Config $config): void
    {
        $prefixedPath = $this->applyPathPrefix($path);
        $prefixedNewPath = $this->applyPathPrefix($newPath);

        $this->client->move($prefixedPath, $prefixedNewPath);
    }
}
