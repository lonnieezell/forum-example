<?php

namespace App\Controllers;

use App\Models\NotificationMutedModel;
use App\Models\NotificationSettingModel;
use App\Models\ThreadModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use InvalidArgumentException;

class ActionsController extends BaseController
{
    /**
     * Enable / disable notifications for the thread.
     */
    public function notifications(int $userId, int $threadId, string $action)
    {
        if (! $this->validateData([
            'userId'   => $userId,
            'threadId' => $threadId,
            'action'   => $action,
        ], [
            'userId'   => ['is_natural_no_zero'],
            'threadId' => ['is_natural_no_zero'],
            'action'   => ['in_list[mute,unmute]'],
        ])) {
            throw new InvalidArgumentException(implode(PHP_EOL, $this->validator->getErrors()));
        }

        $thread = model(ThreadModel::class)->open()->visible()->find($threadId);

        if (! $thread) {
            throw PageNotFoundException::forPageNotFound('This thread does not exist.');
        }

        $setting = model(NotificationSettingModel::class)->withAnyNotification()->find($userId);

        if (! $setting) {
            if ($this->request->is('htmx')) {
                return '';
            }

            throw PageNotFoundException::forPageNotFound('All your notifications are already disabled.');
        }

        $NotificationMutedModel = model(NotificationMutedModel::class);
        if ($action === 'mute') {
            $NotificationMutedModel->insert($userId, $threadId);
        } else {
            $NotificationMutedModel->delete($userId, $threadId);
        }

        if ($this->request->is('htmx')) {
            return view_cell('MuteThreadCell', ['userId' => $userId, 'threadId' => $threadId]);
        }

        return redirect()->to($thread->link());
    }
}
