<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Forum extends BaseConfig
{
    /**
     * The name of the current theme.
     * Must be within /themes directory.
     */
    public string $themeName = 'default';

    /**
     * The number of days after which the
     * user account will be deleted.
     */
    public int $accountDeleteAfter = 7;

    /**
     * The number of seconds after which
     * alert will be removed.
     */
    public int $alertDisplayTime = 5;

    /**
     * The number of months without login
     * after which 2FA will be forced.
     */
    public int $force2faAfter = 12;

    /**
     * The maximum number of reports
     * user may add per day.
     */
    public int $maxReportsPerDey = 20;
}
