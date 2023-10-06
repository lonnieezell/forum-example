<?php

namespace App\Models;

use App\Entities\NotificationSetting;
use CodeIgniter\Model;

class NotificationSettingModel extends Model
{
    protected $table            = 'notification_settings';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = false;
    protected $returnType       = NotificationSetting::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'email_thread', 'email_post', 'email_post_reply'];

    // Dates
    protected $useTimestamps = true;

    /**
     * Get settings with active email thread notifications.
     */
    public function withThreadNotification()
    {
        $this->where('email_thread', 1);

        return $this;
    }

    /**
     * Get settings with active email post notifications.
     */
    public function withPostNotification()
    {
        $this->groupStart()->where('email_post', 1)->orWhere('email_post_reply', 1)->groupEnd();

        return $this;
    }
}
