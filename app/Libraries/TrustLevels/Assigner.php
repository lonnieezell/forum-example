<?php

namespace App\Libraries\TrustLevels;

use App\Entities\User;
use App\Models\UserModel;
use JakubOnderka\PhpParallelLint\RunTimeException;

/**
 * Assigns trust levels to the given users based on their activity and
 * the thresholds defined in the TrustLevels configuration.
 *
 * @package App\Libraries\TrustLevels
 */
class Assigner
{
    private readonly UserModel $userModel;
    private array $levels = [];
    private array $requirements = [];

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
        $this->levels = setting('TrustLevels.levels');
        $this->requirements = setting('TrustLevels.requirements');
    }

    /**
     * Assign trust levels to the users based upon their activity.
     * For each user it will first check if they still meet the requirements
     * for their current trust level. If they don't, it will downgrade them
     * to the next lowest trust level and check them again. If they do, it
     * will check if they meet the requirements for the next highest trust level.
     * If they do, it will upgrade them to that trust level.
     *
     * Example:
     *  $assigner = new Assigner();
     *  $users = $userModel->active()->limit(50)->findAll();
     *  $assigner->assignTrustLevels($users);
     */
    public function assignTrustLevels(User $user): void
    {
        $totalLevels = count(setting('TrustLevels.levels'));
        $originalTrustLevel = $user->trust_level;
        $assigned = false;

        // First check if the user still meets the requirements for their current trust level.
        if ($this->checkTrustLevel($user, $user->trust_level)) {
            // If they do meet the requirements for their current trust level, check
            // if they meet the requirements for the any higher trust levels.
            while ($user->trust_level < $totalLevels - 1) {
                $user->trust_level++;

                if (! $this->checkTrustLevel($user, $user->trust_level)) {
                    $user->trust_level--;
                    break;
                }
            }

            if ($user->trust_level !== $originalTrustLevel) {
                $this->userModel->save($user);
            }

            return;
        }

        // If they don't, downgrade them to the next lowest trust level and check again.
        while ($user->trust_level > 0) {
            $user->trust_level--;

            if ($this->checkTrustLevel($user, $user->trust_level)) {
                $assigned = true;
                break;
            }
        }

        // If they don't meet the requirements for any trust level, assign them the lowest trust level.
        if (! $assigned) {
            $user->trust_level = 0;
        }

        if ($user->trust_level !== $originalTrustLevel) {
            $this->userModel->save($user);
        }
    }

    private function checkTrustLevel(User $user, int $level): bool
    {
        if (! array_key_exists($level, $this->requirements)) {
            throw new RunTimeException("Trust level {$level} does not exist.");
        }

        $requirements = $this->requirements[$level];

        if ($requirements === []) {
            return true;
        }

        foreach ($requirements as $action => $value) {
            $meetsCriteria = match ($action) {
                'new-threads' => $user->thread_count >= $value,
                // 'read-threads' => $user->read_count >= $value,
                'daily-visits' => $user->countDailyVisits() >= $value,
                'likes-given' => $user->countLikesGiven() >= $value,
                'likes-received' => $user->countLikesReceived() >= $value,
                'replies-given' => $user->post_count >= $value,
                default => throw new RunTimeException("Unknown trust level requirement: {$action}"),
            };

            if (! $meetsCriteria) {
                return false;
            }
        }

        return true;
    }
}
