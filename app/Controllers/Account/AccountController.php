<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Authentication\AuthenticationException;
use ReflectionException;

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
