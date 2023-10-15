<?php

namespace App\Jobs;

use Michalsn\CodeIgniterQueue\BaseJob;
use Michalsn\CodeIgniterQueue\Interfaces\JobInterface;

class EmailPostNotification extends BaseJob implements JobInterface
{
    public function process()
    {
        return service('email', false)
            ->setTo($this->data['to'])
            ->setSubject(config('App')->siteName . ' - New post notification')
            ->setMessage($this->data['message'])
            ->send();
    }
}
