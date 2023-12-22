<?php

namespace App\Controllers\Auth;

use CodeIgniter\Shield\Controllers\ActionController as ShieldActionController;

class ActionController extends ShieldActionController
{
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
