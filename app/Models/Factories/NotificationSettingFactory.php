<?php

namespace App\Models\Factories;

use App\Entities\NotificationSetting;
use App\Models\NotificationSettingModel;
use Faker\Generator;

class NotificationSettingFactory extends NotificationSettingModel
{
    /**
     * Factory method to create a fake notification settings.
     */
    public function fake(Generator &$faker): NotificationSetting
    {
        return new NotificationSetting([
            'user_id'          => null,
            'email_thread'     => 0,
            'email_post'       => 0,
            'email_post_reply' => 0,
        ]);
    }
}
