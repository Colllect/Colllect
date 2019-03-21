<?php

declare(strict_types=1);

namespace App\FilesystemAdapter;

use App\EnhancedFlysystemAdapter\EnhancedAwsS3Adapter;
use App\EnhancedFlysystemAdapter\EnhancedFilesystem;
use App\Entity\User;
use Aws\S3\S3Client;
use League\Flysystem\Config;
use League\Flysystem\FilesystemInterface;

class AwsS3 extends AbstractCachedFilesystemAdapter implements FilesystemAdapterInterface
{
    const CACHE_NAME = 'aws_s3';

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $region;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    public function __construct(
        string $cacheRoot,
        int $cacheDuration,
        string $key,
        string $secret,
        string $region,
        string $bucket
    ) {
        parent::__construct($cacheRoot, $cacheDuration);

        $this->key = $key;
        $this->secret = $secret;
        $this->region = $region;
        $this->bucket = $bucket;
    }

    /**
     * {@inheritdoc}
     */
    final protected static function getCacheName(): string
    {
        return self::CACHE_NAME;
    }

    public function getFilesystem(User $user): FilesystemInterface
    {
        if (!$this->filesystem) {
            $client = new S3Client(
                [
                    'credentials' => [
                        'key' => $this->key,
                        'secret' => $this->secret,
                    ],
                    'region' => $this->region,
                    'version' => 'latest',
                ]
            );

            $adapter = $this->cacheAdapter(new EnhancedAwsS3Adapter($client, $this->bucket, $user->getId()), $user);

            $this->filesystem = new EnhancedFilesystem(
                $adapter,
                new Config(
                    [
                        'disable_asserts' => true,
                    ]
                )
            );
        }

        return $this->filesystem;
    }
}
