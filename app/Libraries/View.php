<?php

namespace App\Libraries;

use CodeIgniter\View\View as BaseView;

class View extends BaseView
{
    /**
     * A streamlined version of the include() method that
     * will render a view, but allows us to stay within the same
     * View instance as the parent.
     */
    public function view(string $name, array $data = []): string
    {
        return $this->setData($data)
            ->render($name, null, false);
    }
}
