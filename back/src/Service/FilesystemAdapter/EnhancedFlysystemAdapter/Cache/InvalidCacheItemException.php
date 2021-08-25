<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\Cache;

use League\Flysystem\FilesystemException;
use RuntimeException;

class InvalidCacheItemException extends RuntimeException implements FilesystemException
{
    public static function withPathAndKey(string $path, string $key): self
    {
        return new self(
            sprintf(
                'Could not fetch key "%s" for path "%s"',
                $key,
                $path
            )
        );
    }
}
