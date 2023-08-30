<?php

namespace App\Controllers\Members;

use App\Controllers\BaseController;

/**
 * Class MemberController
 *
 * @package App\Controllers\Members
 */
class MemberController extends BaseController
{

    /**
     * Display a standard forum-style list of users.
     */
    public function list()
    {
        return $this->render('members/list');
    }

}
