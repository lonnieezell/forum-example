<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Entities\User;
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

        /** @var User::class $user */
        $user = auth()->user();

        return $this->render('account/security/security', [
            'user'         => $user,
            'logins'       => $user->logins(5),
            'agent'        => $this->request->getUserAgent(),
            'isRemembered' => model(RememberModel::class)->where('user_id', user_id())->countAllResults() > 0,
            'validator'    => service('validation'),
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
     * Allow a user to change their own password.
     */
    public function changePassword()
    {
        if (! $this->policy->can('users.changePassword', auth()->user())) {
            // user is allowed NOT to edit the post
            return $this->policy->deny('You are not allowed to change the password for this user.');
        }

        helper('form');

        // Validate the user's password.
        $valid = $this->validateData($this->request->getPost(), [
            'current_password' => 'required',
            'password'         => 'required|min_length[' . setting('Auth.minimumPasswordLength') . ']|max_length[255]|strong_password',
            'confirm_password' => 'required|matches[password]',
        ]);

        if (! $valid) {
            return $this->render('account/security/_change_password', [
                'open'      => true,
                'validator' => $this->validator,
            ]);
        }

        // Make sure the current password matches the one in the database.
        $credentials = [
            'email'    => auth()->user()->email,
            'password' => $this->request->getPost('current_password'),
        ];

        $validCreds = auth()->check($credentials);

        if (! $validCreds->isOK()) {
            $this->validator->setError('current_password', 'The password you entered is incorrect.');
        }

        // Make sure the new password is different from the old one.
        if ($this->request->getPost('current_password') === $this->request->getPost('password')) {
            $this->validator->setError('password', 'The new password must be different from the old one.');
        }

        // If there are any errors, display the form again with the errors.
        if (count($this->validator->getErrors()) > 0) {
            return $this->render('account/security/_change_password', [
                'open'      => true,
                'validator' => $this->validator,
            ]);
        }

        // Update the user's password.
        try {
            $user           = auth()->user();
            $user->password = $this->request->getPost('password');
            model(UserModel::class)->save($user);
        } catch (Throwable $e) {
            // Log the error
            log_message('error', $e->getMessage());

            return $this->render('account/security/_change_password', [
                'open'  => true,
                'error' => 'There was an error updating your password. Please try again.',
            ]);
        }

        return $this->render('account/security/_change_password', [
            'message'   => 'Your password has been updated.',
            'open'      => true,
            'validator' => $this->validator,
        ]);
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
