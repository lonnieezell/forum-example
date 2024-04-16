<?php

namespace App\Commands;

use App\Libraries\TrustLevels\Assigner;
use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Runs nightly to scan all users active within the last 4 months
 * and determine their trust level based on their activity.
 *
 * Trust levels are defined in `app/Config/TrustLevels.php`
 */
class SetTrustLevels extends BaseCommand
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
    protected $name = 'trust-levels:set';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Sets the trust levels for active users.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'trust-levels:set';

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
        $assigner = new Assigner();

        $currentStep = 1;
        $updateCount = model(UserModel::class)
            ->active()
            ->activeToday()
            ->countAllResults();

        CLI::write('Updating trust levels for ' . $updateCount . ' users...');

        model(UserModel::class)
            ->active()
            ->activeToday()
            ->chunk(10, static function ($user) use ($assigner, &$currentStep, $updateCount) {
                $assigner->assignTrustLevels($user);

                CLI::showProgress($currentStep++, $updateCount);
            });

        CLI::showProgress(false);

        CLI::write('Trust levels updated.');
    }
}
