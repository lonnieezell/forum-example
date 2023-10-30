<?php

use App\Libraries\Alerts;

if (! function_exists('alerts')) {
    /**
     * Returns Alerts library instance.
     */
    function alerts(): Alerts
    {
        return service('alerts');
    }
}
