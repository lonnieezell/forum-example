<?php

namespace App\Concerns;

trait ThemeRenderer
{
    /**
     * Render a view file.
     *
     * Must be used in order to utilize the theme system.
     */
    protected function render(string $view, array $data = []): string
    {
        $themeName = config('Forum')->themeName;
        $themePath = ROOTPATH . "/themes/{$themeName}/";
        $renderer  = single_service('renderer', $themePath);

        return $renderer
            ->setData($data)
            ->render($view);
    }
}
