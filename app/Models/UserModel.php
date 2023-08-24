<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUser;
use App\Entities\User;

class UserModel extends ShieldUser
{
    protected $returnType = User::class;
}
