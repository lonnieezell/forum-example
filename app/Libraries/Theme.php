<?php

namespace App\Libraries;

use InvalidArgumentException;
use ReflectionException;

class Theme
{
    private string $theme   = 'default';
    private ?View $renderer = null;

    /**
     * Sets the current theme name.
     */
    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Returns the current theme name.
     */
    public function theme(): string
    {
        return $this->theme;
    }

    /**
     * Returns a rendered view, either from the theme,
     * or the app/Views directory if not in current theme.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function render(string $view, array $data = [])
    {
        return $this->renderer()
            ->setData($data)
            ->render($view);
    }

    private function renderer(): View
    {
        if ($this->renderer === null) {
            $themePath      = ROOTPATH . "/themes/{$this->theme}/";
            $this->renderer = single_service('renderer', $themePath);
        }

        return $this->renderer;
    }
}
