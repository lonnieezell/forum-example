<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Forum extends BaseConfig
{
    /**
     * The number of days after which the
     * user account will be deleted.
     */
    public int $accountDeleteAfter = 7;
}
