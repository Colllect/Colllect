<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedAwsS3Adapter;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystem;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedFilesystemInterface;
use Aws\S3\S3Client;
use Exception;
use League\Flysystem\Config;

class AwsS3 extends AbstractCachedFilesystemAdapter implements FilesystemAdapterInterface
{
    private const NAME = 'aws_s3';

    /* @var string */
    private $key;

    /* @var string */
    private $secret;

    /* @var string */
    private $region;

    /* @var string */
    private $bucket;

    /* @var EnhancedFilesystemInterface */
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

    /**
     * @throws Exception
     */
    public function getFilesystem(User $user): EnhancedFilesystemInterface
    {
        if ($this->filesystem === null) {
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

            $userId = $user->getId();

            if ($userId === null) {
                throw new \Exception('awss3.not_logged_in');
            }

            $adapter = $this->cacheAdapter(new EnhancedAwsS3Adapter($client, $this->bucket, (string) $userId), $user);

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
