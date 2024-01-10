<?php

namespace App\Controllers\Admin\Settings;

use App\Controllers\AdminController;
use App\Libraries\Authentication\Actions\Email2FA;
use App\Libraries\Authentication\Actions\TwoFactorAuthEmail;
use CodeIgniter\Shield\Authentication\Actions\EmailActivator;

class UsersController extends AdminController
{
    /**
     * Displays the users settings page.
     */
    public function index()
    {
        $rememberOptions = [
            '1 hour'   => 1 * HOUR,
            '4 hours'  => 4 * HOUR,
            '8 hours'  => 8 * HOUR,
            '25 hours' => 24 * HOUR,
            '1 week'   => 1 * WEEK,
            '2 weeks'  => 2 * WEEK,
            '3 weeks'  => 3 * WEEK,
            '1 month'  => 1 * MONTH,
            '2 months' => 2 * MONTH,
            '6 months' => 6 * MONTH,
            '1 year'   => 12 * MONTH,
        ];

        if ($this->request->is('post')) {
            if ($this->validate([
                'minimumPasswordLength' => 'required|integer|greater_than[6]',
            ])) {
                setting('Auth.allowRegistration', (bool) $this->request->getPost('allowRegistration'));
                setting('Auth.passwordValidators', $this->request->getPost('validators'));

                // Actions
                $actions             = setting('Auth.actions');
                $actions['login']    = $this->request->getPost('email2FA') ? Email2FA::class : TwoFactorAuthEmail::class;
                $actions['register'] = $this->request->getPost('emailActivation') ? EmailActivator::class : null;
                setting('Auth.actions', $actions);

                // Remember Me
                $sessionConfig                     = setting('Auth.sessionConfig');
                $sessionConfig['allowRemembering'] = $this->request->getPost('allowRemember') ? true : false;
                $sessionConfig['rememberLength']   = $this->request->getPost('rememberLength');
                setting('Auth.sessionConfig', $sessionConfig);

                alerts()->set('success', 'Settings saved');
            }
        }

        $view = $this->request->isHtmx() ? 'admin/settings/_users_form' : 'admin/settings/users';

        return $this->render($view, [
            'rememberOptions' => $rememberOptions,
            'validator'       => $this->validator ?? service('validation'),
        ]);
    }
}
