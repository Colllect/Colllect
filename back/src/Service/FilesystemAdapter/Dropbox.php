<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedDropboxAdapter;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystem;
use App\Entity\User;
use App\Exception\DropboxAccessTokenMissingException;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\Config;
use League\Flysystem\FilesystemInterface;
use Spatie\Dropbox\Client as DropboxClient;

class Dropbox extends AbstractCachedFilesystemAdapter implements FilesystemAdapterInterface
{
    private const NAME = 'dropbox';

    private $filesystem;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, string $fsCacheRoot, int $fsCacheDuration)
    {
        parent::__construct($fsCacheRoot, $fsCacheDuration);

        $this->entityManager = $entityManager;
    }

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
    public function getFilesystem(User $user): FilesystemInterface
    {
        if (!$this->filesystem) {
            $userFilesystemCredentials = $user->getFilesystemCredentials();

            if (!$userFilesystemCredentials
                || $userFilesystemCredentials->getFilesystemProviderName() !== self::getName()) {
                throw new DropboxAccessTokenMissingException('error.dropbox_not_linked');
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
}
