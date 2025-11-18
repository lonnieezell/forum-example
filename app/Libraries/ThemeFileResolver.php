<?php

namespace App\Libraries;

/**
 * Resolves theme view files with fallback support.
 *
 * Attempts to locate view files in multiple theme directories,
 * checking the current theme first, then falling back to
 * additional theme paths in order.
 */
class ThemeFileResolver
{
    /**
     * Array of theme paths to check, in priority order.
     *
     * @var list<string>
     */
    private array $themePaths = [];

    /**
     * Constructor.
     *
     * @param string   $currentThemePath The primary theme path to search
     * @param string[] $fallbackPaths    Additional theme paths to check if file not found in current
     */
    public function __construct(string $currentThemePath, array $fallbackPaths = [])
    {
        // Ensure paths end with directory separator
        $this->themePaths[] = rtrim($currentThemePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        foreach ($fallbackPaths as $path) {
            $this->themePaths[] = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }
    }

    /**
     * Resolve a view file by checking each theme path in order.
     *
     * @param string $viewName The view file to locate (e.g., 'discussions/list' or 'discussions/list.php')
     *
     * @return string|false The full path to the first matching file, or false if not found in any theme
     */
    public function resolve(string $viewName): string|false
    {
        // Ensure the view name has a .php extension
        $fileName = pathinfo($viewName, PATHINFO_EXTENSION) === '' ? $viewName . '.php' : $viewName;

        foreach ($this->themePaths as $themePath) {
            $filePath = $themePath . $fileName;

            if (is_file($filePath)) {
                return $filePath;
            }
        }

        return false;
    }
}
