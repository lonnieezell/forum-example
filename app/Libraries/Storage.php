<?php

namespace App\Libraries;

use Aws\S3\S3Client;
use Config\FileSystems;
use InvalidArgumentException;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

/**
 * Provides a wrapper around the Flysystem library.
 */
class Storage
{
    private array $filesystems = [];
    private string $default;
    private FileSystems $config;

    public function __construct(FileSystems $config)
    {
        $this->config  = $config;
        $this->default = $config->default;
    }

    /**
     * Get a filesystem instance.
     */
    public function disk(string $disk = 'default'): FlySystem
    {
        if ($disk === 'default') {
            $disk = $this->default;
        }

        if (! isset($this->filesystems[$disk])) {
            $this->filesystems[$disk] = $this->createDisk($disk);
        }

        return $this->filesystems[$disk];
    }

    /**
     * Create a new filesystem instance.
     * @return League\Flysystem\Filesystem
     * @throws InvalidArgumentException
     */
    private function createDisk(string $disk): FileSystem
    {
        $config = $this->config->disks[$disk] ?? null;

        if (empty($config)) {
            throw new InvalidArgumentException('Disk not found: ' . $disk);
        }

        $driver = $config['driver'] ?? null;

        if (empty($driver)) {
            throw new InvalidArgumentException('Driver not found for disk: ' . $disk);
        }

        switch ($driver) {
            case 'local':
                return new Filesystem(new LocalFilesystemAdapter($config['root']));
            case 's3':
                /** @var Aws\S3\S3ClientInterface $client */
                $client = new S3Client([
                    'region' => $config['region'],
                ]);
                return new Filesystem(new AwsS3V3Adapter(
                    $client,                    // S3Client
                    $config['bucket'],          // S3 bucket name
                    $config['prefix'] ?? null   // optional path/prefix
                ));
            default:
                throw new InvalidArgumentException('Filesystem driver not supported: ' . $driver);
        }
    }
}
