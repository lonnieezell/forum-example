<?php

namespace App\Cells;

use App\Entities\NotificationMuted;
use App\Entities\NotificationSetting;
use App\Models\NotificationMutedModel;
use App\Models\NotificationSettingModel;
use CodeIgniter\View\Cells\Cell;

class MuteThreadCell extends Cell
{
    public ?NotificationSetting $setting          = null;
    public ?NotificationMuted $notificationStatus = null;
    public ?int $threadId                         = null;
    public ?int $userId                           = null;

    public function mount(int $threadId, int $userId)
    {
        helper('text');

        $this->threadId = $threadId;
        $this->userId   = $userId;
        $this->setting  = model(NotificationSettingModel::class)->withAnyNotification()->find($userId);
        if ($this->setting) {
            $this->notificationStatus = model(NotificationMutedModel::class)->find($userId, $threadId);
        }
    }
}
