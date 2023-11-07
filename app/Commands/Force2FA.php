<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;
use Exception;

class Force2FA extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Forum';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = '2fa:force';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Force 2FA for users inactive for X months';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = '2fa:force <months>';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'months' => 'After how many months without login 2FA should be forced',
    ];

    /**
     * Actually execute a command.
     *
     * @throws Exception
     */
    public function run(array $params)
    {
        $months = array_shift($params) ?? 12;
        $date   = Time::now()->subMonths($months)->format('Y-m-d H:i:s');

        $db = db_connect();
        $db->table('users')
            ->where('last_active <=', $date)
            ->where('two_factor_auth_email', 0)
            ->where('deleted_at', null)
            ->update(['two_factor_auth_email' => 1]);

        $affected = $db->affectedRows();

        CLI::write(sprintf('Forcing 2FA for users inactive longer than %d months finished.', $months), 'green');
        CLI::write(sprintf('Users affected: %d', $affected), 'light_yellow');
    }
}
