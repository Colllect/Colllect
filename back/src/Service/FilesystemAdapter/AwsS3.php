<?php

declare(strict_types=1);

namespace App\Service\FilesystemAdapter;

use App\Entity\User;
use App\Service\FilesystemAdapter\EnhancedFilesystem\EnhancedFilesystem;
use App\Service\FilesystemAdapter\EnhancedFilesystem\EnhancedFilesystemInterface;
use App\Service\FilesystemAdapter\EnhancedFlysystemAdapter\EnhancedAwsS3Adapter;
use Aws\S3\S3Client;
use Exception;

class AwsS3 extends AbstractCachedFilesystemAdapter implements FilesystemAdapterInterface
{
    private const NAME = 'aws_s3';

    private string $key;
    private string $secret;
    private string $region;
    private string $bucket;
    private ?EnhancedFilesystemInterface $filesystem = null;

    public function __construct(
        int $fsCacheDuration,
        string $fsAwsKey,
        string $fsAwsSecret,
        string $fsAwsRegion,
        string $fsAwsBucket
    ) {
        parent::__construct($fsCacheDuration);

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
                throw new Exception('aws_s3.not_logged_in');
            }

            $adapter = $this->cachedAdapter(new EnhancedAwsS3Adapter($client, $this->bucket, (string) $userId), $user);

            $this->filesystem = new EnhancedFilesystem(
                $adapter,
                [
                    'disable_asserts' => true,
                ]
            );
        }

        return $this->filesystem;
    }
}
