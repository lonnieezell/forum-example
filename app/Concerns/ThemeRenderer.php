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
        $defaultThemePath = ROOTPATH . "/themes/default/";
        $renderer  = single_service('renderer', $themePath, $defaultThemePath);

        return $renderer
            ->setData($data)
            ->render($view);
    }
}
