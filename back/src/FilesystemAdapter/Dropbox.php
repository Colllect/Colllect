<?php

declare(strict_types=1);

namespace App\FilesystemAdapter;

use App\EnhancedFlysystemAdapter\EnhancedDropboxAdapter;
use App\EnhancedFlysystemAdapter\EnhancedFilesystem;
use App\Entity\User;
use App\Exception\DropboxAccessTokenMissingException;
use League\Flysystem\Config;
use League\Flysystem\FilesystemInterface;
use Spatie\Dropbox\Client as DropboxClient;

class Dropbox extends AbstractCachedFilesystemAdapter implements FilesystemAdapterInterface
{
    const CACHE_NAME = 'dropbox';

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * {@inheritdoc}
     */
    final protected static function getCacheName(): string
    {
        return self::CACHE_NAME;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DropboxAccessTokenMissingException
     */
    public function getFilesystem(User $user): FilesystemInterface
    {
        if (!$this->filesystem) {
            $token = $user->getDropboxToken();

            if (!$token) {
                throw new DropboxAccessTokenMissingException('error.dropbox_not_linked');
            }

            $client = new DropboxClient($token);
            $adapter = $this->cacheAdapter(new EnhancedDropboxAdapter($client), $user);

            $this->filesystem = new EnhancedFilesystem(
                $adapter,
                new Config(
                    [
                        'case_sensitive' => false,
                        'disable_asserts' => true,
                    ]
                )
            );
        }

        return $this->filesystem;
    }
}
