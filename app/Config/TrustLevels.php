<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class TrustLevels extends BaseConfig
{
    public const THREAD_THRESHOLD = 3;
    public const POST_THRESHOLD = 10;

    /**
     * The actions that trust levels can check against.
     */
    public $actions = [
        'report'           => 'Report a thread/post',
        'attach'           => 'Attach a file to a thread/post',
        'link-signature'   => 'Have any links in their signature',
        'start-discussion' => 'Start more than '. self::THREAD_THRESHOLD .' new discussions',
        'reply'            => 'Post more than '. self::POST_THRESHOLD .' replies',
        'edit-own'         => 'Edit their own thread/post after 24 hours',
        'send-pm'          => 'Send private messages',
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
            'report',
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
            'new-threads'  => 5,
            // 'read-threads' => 30,
        ],
        2 => [
            'daily-visits'   => 15,
            'likes-given'    => 1,
            'likes-received' => 1,
            'replies-given'  => 3,
            'new-threads'    => 20,
            // 'read-threads'   => 100,
        ],
        3 => [
            'daily-visits'   => 50,
            'likes-given'    => 20,
            'likes-received' => 30,
            'replies-given'  => 10,
            // 'read-threads'   => 100,
        ],
    ];
}
