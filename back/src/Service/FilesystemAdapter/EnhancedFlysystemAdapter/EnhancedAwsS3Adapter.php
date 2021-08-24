<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter\EnhancedFlysystemAdapter;

use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Config;

class EnhancedAwsS3Adapter extends AwsS3V3Adapter implements EnhancedFlysystemAdapterInterface
{
    public function renameDir(string $path, string $newPath, Config $config): void
    {
        $this->move($path, $newPath, $config);
    }
}
