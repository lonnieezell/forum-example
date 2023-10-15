<?php

namespace Config;

use App\Jobs\EmailPostNotification;
use Michalsn\CodeIgniterQueue\Config\Queue as BaseQueue;
use Michalsn\CodeIgniterQueue\Handlers\DatabaseHandler;

class Queue extends BaseQueue
{
    /**
     * Default handler.
     */
    public string $defaultHandler = 'database';

    /**
     * Available handlers.
     */
    public array $handlers = [
        'database' => DatabaseHandler::class,
    ];

    /**
     * Database handler config.
     */
    public array $database = [
        'dbGroup'   => 'default',
        'getShared' => true,
    ];

    /**
     * Whether to keep the DONE jobs in the queue.
     */
    public bool $keepDoneJobs = false;

    /**
     * Whether to save failed jobs for later review.
     */
    public bool $keepFailedJobs = true;

    /**
     * Your jobs handlers.
     */
    public array $jobHandlers = [
        'email-post-notification' => EmailPostNotification::class,
    ];
}
