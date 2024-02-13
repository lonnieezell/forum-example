<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class TrustLevels extends BaseConfig
{
    /**
     * The actions that trust levels can check against.
     */
    public $actions = [
        'flag' => 'Flag a thread/post',
        'attach' => 'Attach a file to a thread/post',
        'link-signature' => 'Have any links in their signature',
        'start-discussion' => 'Start more than 3 new discussions',
        'reply' => 'Post more than 10 replies',
        'edit-own' => 'Edit their own thread/post after 24 hours',
        'send-pm' => 'Send private messages',
    ];

    /**
     * The levels of trust that users can have.
     */
    public $levels = [
        0 => 'New Member',
        1 => 'Member',
        2 => 'Regular',
        3 => 'Leader',
    ];

    /**
     * The actions that each trust level is allowed to perform.
     */
    public $allowedActions = [
        0 => [],
        1 => [
            'flag',
            'attach',
            'link-signature',
            'start-discussion',
            'reply',
            'edit-own',
            'send-pm',
        ],
    ];

    /**
     * The requirements that a user has to meet to reach a trust level.
     */
    public $requirements = [
        0 => [],
        1 => [
            'new-threads' => 5,
            'read-threads' => 30,
        ],
        2 => [
            'daily-visits' => 15,
            'likes-given' => 1,
            'likes-received' => 1,
            'replies-given' => 3,
            'new-threads' => 20,
            'read-threads' => 100,
        ],
    ];
}
