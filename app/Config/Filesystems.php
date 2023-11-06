<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class FileSystems extends BaseConfig
{
    /**
     * The default filesystem that will
     * be used if no others are specified.
     */
    public string $default = env('FILESYSTEM_DEFAULT', 'local');

    /**
     * Default settings for each available, with credentials, if needed.
     */
    public $disks = [
        'local' => [
            'driver' => 'local',
            'root' => WRITEPATH . 'uploads',
        ],
        'public' => [
            'driver' => 'local',
            'root' => PUBLICPATH . 'uploads',
        ],
        // This driver requires the following environment variables:
        // AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_REGION, AWS_BUCKET
        's3' => [
            'driver' => 's3',
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'prefix' => env('AWS_PREFIX'),
        ],
    ];
}
