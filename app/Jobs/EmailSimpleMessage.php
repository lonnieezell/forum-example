<?php

namespace App\Jobs;

use CodeIgniter\Queue\BaseJob;
use CodeIgniter\Queue\Interfaces\JobInterface;
use Exception;

class EmailSimpleMessage extends BaseJob implements JobInterface
{
    public function process()
    {
        $email  = service('email', null, false);
        $result = $email
            ->setTo($this->data['to'])
            ->setSubject($this->data['subject'])
            ->setMessage($this->data['message'])
            ->send(false);

        if (! $result) {
            throw new Exception($email->printDebugger('headers'));
        }

        return $result;
    }
}
