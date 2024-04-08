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
        // email reminder about incoming user deletion
        $schedule->command('accounts:delete:reminder 1,3')->daily('10:00 am')->named('accounts-delete-reminder');
        // actual user deletion
        $schedule->command('accounts:delete')->daily('11:00 am')->named('accounts-delete');
        // cleanup unused images
        $schedule->command('cleanup:images 3')->hourly()->named('cleanup-images');

        // daily moderation summary
        $schedule->command('moderation:summary')->daily('11:50 pm')->named('daily-moderation-summary');

        // always set at the last position, so that other tasks can be executed first
        $schedule->command('queue:work emails -max 20 --stop-when-empty')->everyMinute()->named('queue-emails');

        // update trust levels daily
        $schedule->command('trust-levels:set')->daily('1:00 am')->named('trust-levels-set');
    }
}
