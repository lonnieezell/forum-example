<?php

declare(strict_types=1);

namespace App\Libraries\Authentication\Actions;

use CodeIgniter\Shield\Authentication\Actions\ActionInterface;
use CodeIgniter\Shield\Authentication\Actions\Email2FA;
use CodeIgniter\Shield\Entities\User;

/**
 * Class TwoFactorAuthEmail
 *
 * Sends an email to the user with a code to verify their account.
 */
class TwoFactorAuthEmail extends Email2FA implements ActionInterface
{
    /**
     * Creates an identity for the action of the user.
     *
     * @return string secret
     */
    public function createIdentity(User $user): string
    {
        if (! $user->two_factor_auth_email) {
            return '';
        }

        return parent::createIdentity($user);
    }
}
