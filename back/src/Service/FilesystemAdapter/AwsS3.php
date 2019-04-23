<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedAwsS3Adapter;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystem;
use App\Entity\User;
use Aws\S3\S3Client;
use League\Flysystem\Config;
use League\Flysystem\FilesystemInterface;

class AwsS3 extends AbstractCachedFilesystemAdapter implements FilesystemAdapterInterface
{
    private const NAME = 'aws_s3';

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
        string $fsCacheRoot,
        int $fsCacheDuration,
        string $fsAwsKey,
        string $fsAwsSecret,
        string $fsAwsRegion,
        string $fsAwsBucket
    ) {
        parent::__construct($fsCacheRoot, $fsCacheDuration);

        $this->key = $fsAwsKey;
        $this->secret = $fsAwsSecret;
        $this->region = $fsAwsRegion;
        $this->bucket = $fsAwsBucket;
    }

    /**
     * {@inheritdoc}
     */
    final public static function getName(): string
    {
        return self::NAME;
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
