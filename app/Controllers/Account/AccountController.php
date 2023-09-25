<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;

class AccountController extends BaseController
{
    /**
     * Display the main user account page.
     */
    public function index()
    {
        return $this->render('account/index', [
            'user' => auth()->user(),
        ]);
    }
}
