<?php

namespace App\Commands;

use Exception;
use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;

class UpdateVisits extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Trust Levels';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'trust-levels:visits';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Creates a permenant record of a user visiting the site.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'trust-levels:visits';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $db = db_connect();

        // Create a new entry in the user_visits table for
        // all users that have a last_active date of today.
        model(UserModel::class)
            ->where('last_active', date('Y-m-d'))
            ->chunk(100, static function ($users) use ($db) {
                foreach ($users as $user) {
                    try {
                        $db->table('user_visits')
                            ->insert([
                                'user_id' => $user->id,
                                'visited_on' => date('Y-m-d'),
                            ]);
                    } catch (Exception $e) {
                        log_message('error', 'Error updating user visits: '. $e->getMessage());
                    }
                }
            });


        // Now clean up any old records that are more than 100 days old.
        $db->table('user_visits')
            ->where('visited_on <', Time::now()->subDays(100)->toDateString())
            ->delete();
    }
}
