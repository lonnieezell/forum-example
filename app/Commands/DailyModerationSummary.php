<?php

namespace App\Commands;

use App\Models\ModerationLogModel;
use App\Models\ModerationReportModel;
use App\Models\NotificationSettingModel;
use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;
use Exception;

class DailyModerationSummary extends BaseCommand
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
    protected $name = 'moderation:summary';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Send a daily moderation summary with stats';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'moderation:summary';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $users = model(NotificationSettingModel::class)->where('moderation_daily_summary', 1)->findAll();

        if ($users === []) {
            CLI::write('No users to notify');
            return EXIT_SUCCESS;
        }

        // Prepare summary
        $summary = $this->summary();

        // Prepare users
        $users = array_column($users, 'user_id');
        $users = model(UserModel::class)->whereIn('id', $users)->findAll();

        $queue = service('queue');

        foreach ($users as $user) {
            $queue->push('emails', 'email-simple-message', [
                'to'      => $user->email,
                'subject' => 'Daily moderation summary stats',
                'message' => view('_emails/daily_moderation_summary', [
                    'user'    => $user,
                    'summary' => $summary,
                ]),
            ]);
        }

        CLI::write(sprintf('%s emails has been sent', count($users)), 'green');
        return EXIT_SUCCESS;
    }

    /**
     * Prepare summary.
     *
     * @throws Exception
     */
    protected function summary(): array
    {
        return [
            'waiting' => $this->waitingInQueue(),
            'thread'  => $this->moderatedToday('thread'),
            'post'    => $this->moderatedToday('post'),
        ];
    }

    /**
     * Waiting posts and threads for moderation.
     */
    protected function waitingInQueue(): array
    {
        $waiting = model(ModerationReportModel::class)
            ->builder()
            ->select('COUNT(*) AS count, resource_type')
            ->groupBy('resource_type')
            ->get()
            ->getResultArray();

        return array_column($waiting, 'count', 'resource_type');
    }

    /**
     * Moderated items by action status.
     *
     * @throws Exception
     */
    protected function moderatedToday(string $resourceType): array
    {
        $moderated = model(ModerationLogModel::class)
            ->builder()
            ->select('COUNT(*) AS count, status')
            ->where('DATE(created_at)', Time::now()->format('Y-m-d'))
            ->where('resource_type', $resourceType)
            ->groupBy('status')
            ->get()
            ->getResultArray();

        return array_column($moderated, 'count', 'status');
    }
}
