<?php

namespace App\Libraries;

use CodeIgniter\View\View as BaseView;

class View extends BaseView
{
    /**
     * Theme file resolver for cascading theme lookups.
     *
     * @var ThemeFileResolver|null
     */
    private ?ThemeFileResolver $themeResolver = null;

    /**
     * Set the theme file resolver for view resolution.
     *
     * @return $this
     */
    public function setThemeResolver(?ThemeFileResolver $resolver): self
    {
        $this->themeResolver = $resolver;

        return $this;
    }

    /**
     * Builds the output based upon a file name and any
     * data that has already been set.
     *
     * Overrides parent to support theme fallback resolution.
     *
     * @param string                    $view     File name of the view source
     * @param array<string, mixed>|null $options  Reserved for 3rd-party uses
     * @param bool|null                 $saveData If true, saves data for subsequent calls
     */
    public function render(string $view, ?array $options = null, ?bool $saveData = null): string
    {
        $fileExt = pathinfo($view, PATHINFO_EXTENSION);
        $viewFile = ($fileExt === '') ? $view . '.php' : $view;

        // Try to resolve using theme resolver first
        if ($this->themeResolver !== null) {
            $resolvedPath = $this->themeResolver->resolve($viewFile);

            if ($resolvedPath !== false) {
                // Temporarily set the view path to the resolved file's directory
                $originalViewPath = $this->viewPath;
                $this->viewPath = dirname($resolvedPath) . DIRECTORY_SEPARATOR;

                // Adjust the view name to just the filename
                $viewName = basename($resolvedPath);

                // Render using parent logic
                $output = parent::render($viewName, $options, $saveData);

                // Restore original view path
                $this->viewPath = $originalViewPath;

                return $output;
            }
        }

        // Fall back to parent's render if theme resolver didn't find the file
        return parent::render($view, $options, $saveData);
    }

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
