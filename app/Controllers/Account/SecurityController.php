<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\Events\Events;
use CodeIgniter\Shield\Authentication\AuthenticationException;
use CodeIgniter\Shield\Models\RememberModel;
use Throwable;

class SecurityController extends BaseController
{
    /**
     * Manage the user's account security.
     */
    public function index()
    {
        helper('form');

        return $this->render('account/security/security', [
            'user'         => auth()->user(),
            'logins'       => auth()->user()->logins(5),
            'agent'        => $this->request->getUserAgent(),
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

    /**
     * Deletes the user's account.
     */
    public function deleteAccount()
    {
        if (! $this->policy->can('users.delete', auth()->user())) {
            // user is allowed NOT to edit the post
            return $this->policy->deny('You are not allowed to delete this user.');
        }

        helper('form');

        // Validate the user's password.
        $credentials = [
            'email'    => auth()->user()->email,
            'password' => $this->request->getPost('password'),
        ];

        $validCreds = auth()->check($credentials);

        if (! $validCreds->isOK()) {
            return view('account/security/_delete', [
                'open'  => true,
                'error' => 'The password you entered is incorrect.',
            ]);
        }

        try {
            $user = auth()->user();
            Events::trigger('account-deleting', $user);

            model(UserModel::class)->delete($user->id, true);
            auth()->logout();

            Events::trigger('account-deleted', $user);
        } catch (Throwable $e) {
            // Log the error
            log_message('error', $e->getMessage());

            return view('account/security/_delete', [
                'open'  => true,
                'error' => 'There was an error deleting your account. Please try again.',
            ]);
        }

        return redirect()
            ->hxLocation(route_to('login'));
    }
}
