<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Exception\DropboxAccessTokenMissingException;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedDropboxAdapter;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystem;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use League\Flysystem\Config;
use Spatie\Dropbox\Client as DropboxClient;

class Dropbox extends AbstractCachedFilesystemAdapter implements FilesystemAdapterInterface
{
    private const NAME = 'dropbox';

    private ?EnhancedFilesystemInterface $filesystem;

    /**
     * {@inheritdoc}
     */
    final public static function getName(): string
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DropboxAccessTokenMissingException
     */
    public function getFilesystem(User $user): EnhancedFilesystemInterface
    {
        if ($this->filesystem === null) {
            $userFilesystemCredentials = $user->getFilesystemCredentials();

            if ($userFilesystemCredentials === null
                || $userFilesystemCredentials->getFilesystemProviderName() !== self::getName()) {
                throw $this->createTokenMissingException();
            }

            $accessToken = $userFilesystemCredentials->getCredentials();
            $client = new DropboxClient($accessToken);
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

    private function createTokenMissingException(): DropboxAccessTokenMissingException
    {
        return new DropboxAccessTokenMissingException('error.dropbox_not_linked');
    }
}
