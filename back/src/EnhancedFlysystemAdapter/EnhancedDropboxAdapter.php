<?php

declare(strict_types=1);

namespace App\EnhancedFlysystemAdapter;

use Spatie\FlysystemDropbox\DropboxAdapter;

class EnhancedDropboxAdapter extends DropboxAdapter implements EnhancedFlysystemAdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function renameDir(string $path, string $newPath): bool
    {
        $prefixedPath = $this->applyPathPrefix($path);
        $prefixedNewPath = $this->applyPathPrefix($newPath);

        $this->client->move($prefixedPath, $prefixedNewPath);

        return true;
    }
}
