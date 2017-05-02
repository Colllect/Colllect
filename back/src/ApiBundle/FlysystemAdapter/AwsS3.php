<?php

namespace ApiBundle\FlysystemAdapter;

use ApiBundle\Entity\User;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class AwsS3 extends FlysystemAdapter implements FlysystemAdapterInterface
{
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


    /**
     * @param string $key
     * @param string $secret
     * @param string $region
     * @param string $bucket
     */
    public function __construct($key, $secret, $region, $bucket)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->region = $region;
        $this->bucket = $bucket;
    }


    /**
     * @param User $user
     * @return FilesystemInterface
     */
    public function getFilesystem(User $user)
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

            $adapter = $this->cacheAdapter(new AwsS3Adapter($client, $this->bucket, $user->getId()), $user);

            $this->filesystem = new Filesystem(
                $adapter, new Config(
                    [
                        'disable_asserts' => true,
                    ]
                )
            );
        }

        return $this->filesystem;
    }
}
