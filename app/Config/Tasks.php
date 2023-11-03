<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Tasks\Scheduler;

class Tasks extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Should performance metrics be logged
     * --------------------------------------------------------------------------
     *
     * If true, will log the time it takes for each task to run.
     * Requires the settings table to have been created previously.
     */
    public bool $logPerformance = false;

    /**
     * --------------------------------------------------------------------------
     * Maximum performance logs
     * --------------------------------------------------------------------------
     *
     * The maximum number of logs that should be saved per Task.
     * Lower numbers reduced the amount of database required to
     * store the logs.
     */
    public int $maxLogsPerTask = 10;

    /**
     * Register any tasks within this method for the application.
     * Called by the TaskRunner.
     */
    public function init(Scheduler $schedule)
    {
        $schedule->command('accounts:delete:reminder 1,3')->daily('10:00 am')->named('accounts-delete-reminder');

        $schedule->command('accounts:delete')->daily('11:00 am')->named('accounts-delete');

        $schedule->command('cleanup:images 3')->hourly()->named('cleanup-images');

        // always set at the last position, so that other tasks can be executed first
        $schedule->command('queue:work emails -max 20 --stop-when-empty')->everyMinute()->named('queue-emails');
    }
}
