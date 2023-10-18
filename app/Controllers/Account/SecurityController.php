<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use CodeIgniter\Shield\Authentication\AuthenticationException;
use CodeIgniter\Shield\Models\RememberModel;
use ReflectionException;

class SecurityController extends BaseController
{
    /**
     * Manage the user's account security.
     */
    public function index()
    {
        return $this->render('account/security/security', [
            'user' => auth()->user(),
            'logins' => auth()->user()->logins(7),
            'agent' => $this->request->getUserAgent(),
            'isRemembered' => model(RememberModel::class)->where('user_id', user_id())->countAllResults() > 0,
        ]);
    }

    /**
     * Logs the user out of all sessions, by deleting all remember-me tokens.
     * Logs the user out and redirects the user to the login page.
     */
    public function logoutAll()
    {
        try {
            auth()->forget(auth()->user());
            auth()->logout();
        } catch (AuthenticationException $e) {
            return redirect()->back()->with('error', lang('Auth.logoutAllError'));
        }

        return redirect()
            ->hxRedirect(route_to('login'))
            ->with('success', 'You have been logged out of all sessions.');
    }
}
