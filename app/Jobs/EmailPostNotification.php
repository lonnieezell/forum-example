<?php

namespace App\Jobs;

use Exception;
use Michalsn\CodeIgniterQueue\BaseJob;
use Michalsn\CodeIgniterQueue\Interfaces\JobInterface;

class EmailPostNotification extends BaseJob implements JobInterface
{
    public function process()
    {
        $email  = service('email', null, false);
        $result = $email
            ->setTo($this->data['to'])
            ->setSubject(config('App')->siteName . ' - New post notification')
            ->setMessage($this->data['message'])
            ->send(false);

        if (! $result) {
            throw new Exception($email->printDebugger('headers'));
        }

        return $result;
    }
}
