<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class FileSystems extends BaseConfig
{
    /**
     * The default filesystem that will
     * be used if no others are specified.
     */
    public string $default = 'public';

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
            'root' => ROOTPATH . 'public/uploads',
        ],
        // This driver requires the following environment variables:
        // AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_REGION, AWS_BUCKET
        's3' => [
            'driver' => 's3',
            'region' => '',
            'bucket' => '',
            'prefix' => '',
        ],
    ];
}
