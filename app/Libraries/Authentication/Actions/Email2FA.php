<?php

declare(strict_types=1);

namespace App\Libraries\Authentication\Actions;

use App\Concerns\ThemeRenderer;
use CodeIgniter\Shield\Authentication\Actions\ActionInterface;
use CodeIgniter\Shield\Authentication\Actions\Email2FA as BaseEmail2FA;

/**
 * Class Email2FA - extended with view method
 *
 * Sends an email to the user with a code to verify their account.
 */
class Email2FA extends BaseEmail2FA implements ActionInterface
{
    use ThemeRenderer;

    /**
     * Provides a way for third-party systems to simply override
     * the way the view gets converted to HTML to integrate with their
     * own templating systems.
     */
    protected function view(string $view, array $data = [], array $options = []): string
    {
        return $this->render($view, $data);
    }
}
