<?php

namespace App\Commands;

use App\Entities\User;
use App\Models\UserDeleteModel;
use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;

class AccountsDeleteReminder extends BaseCommand
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
    protected $name = 'accounts:delete:reminder';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Sending reminder emails for scheduled account deletions';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'accounts:delete:reminder <days>';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'days' => 'How many days before deletion the reminder should be sent',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $days = array_shift($params) ?? 4;
        $days = array_map('intval', explode(',', $days));

        // Get active users to delete
        $userDeletedModel = model(UserDeleteModel::class);
        $scheduled        = $userDeletedModel->active()->findAll();

        if ($scheduled === []) {
            CLI::write('No accounts scheduled to delete.');
            return EXIT_SUCCESS;
        }

        // Filter out users who should not receive a notification today
        $now       = Time::now();
        $scheduled = array_filter($scheduled, function ($user) use ($now, $days) {
            return in_array($now->difference($user->scheduled_at)->getDays(), $days, true);
        });

        if ($scheduled === []) {
            CLI::write('No accounts scheduled to delete for today.');
            return EXIT_SUCCESS;
        }

        $scheduledIds = array_column($scheduled, 'user_id');
        $users        = model(UserModel::class)
            ->allowCallbacks(false)
            ->onlyDeleted()
            ->whereIn('id', $scheduledIds)
            ->findAll();

        foreach ($users as $user) {
            if ($this->sendNotification($user)) {
                CLI::write(sprintf('User %s has been notified', $user->id), 'green');
            }
        }

        CLI::write('Finished sending accounts removal reminders.', 'green');
        CLI::write('Notified accounts: ' . count($users), 'light_yellow');

        return EXIT_SUCCESS;
    }

    /**
     * Send notification.
     */
    protected function sendNotification(User $user)
    {
        return service('queue')->push('emails', 'email-simple-message', [
            'to'      => $user->email,
            'subject' => 'Your account will be removed soon',
            'message' => view('_emails/account_delete_reminder', [
                'user' => $user
            ]),
        ]);
    }
}
