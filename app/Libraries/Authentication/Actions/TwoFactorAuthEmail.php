<?php

declare(strict_types=1);

namespace App\Libraries\Authentication\Actions;

use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Authentication\Actions\ActionInterface;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserIdentityModel;
use Config\Forum;
use Exception;

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
     *
     * @throws Exception
     */
    public function createIdentity(User $user): string
    {
        if ($user->two_factor_auth_email) {
            return parent::createIdentity($user);
        }

        // Last login date or account creation date
        $date = $user->lastLogin()?->date ?? $user->created_at;
        if ($date->difference(Time::now())->getMonths() >= config(Forum::class)->force2faAfter
            || model(UserIdentityModel::class)->getIdentityByType($user, $this->getType()) !== null) {
            return parent::createIdentity($user);
        }

        return '';
    }
}
