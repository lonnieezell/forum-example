<?php

namespace App\Events;

use App\Entities\User;
use App\Entities\UserDelete;
use App\Models\UserDeleteModel;
use CodeIgniter\I18n\Time;
use Config\Forum;

class AccountDeletedEvent
{
    protected UserDelete $userDelete;

    public function __construct(protected User $user)
    {
        $userDeleteModel = model(UserDeleteModel::class);
        $scheduledId     = $userDeleteModel->insert([
            'user_id'      => $user->id,
            'scheduled_at' => Time::now()
                ->addDays(config(Forum::class)->accountDeleteAfter)
                ->format('Y-m-d 00:00:00'),
        ]);

        /** @var UserDelete $this->userDelete */
        $this->userDelete = $userDeleteModel->find($scheduledId);
    }

    /**
     * Process event.
     */
    public function process(): bool
    {
        return $this->sendNotification($this->user, $this->userDelete);
    }

    /**
     * Send email notification.
     */
    private function sendNotification(User $user, UserDelete $userDelete): bool
    {
        helper('text');

        return service('queue')->push('emails', 'email-simple-message', [
            'to'      => $user->email,
            'subject' => 'Your account has been scheduled to delete',
            'message' => view('_emails/account_delete_scheduled', [
                'user' => $user, 'userDelete' => $userDelete,
            ]),
        ]);
    }
}
