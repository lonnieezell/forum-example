<?php

namespace App\Libraries;

use InvalidArgumentException;

/**
 * Provides HTML for assets compiled for Vite.
 *
 * Specify the source file as the paths.
 * In development mode, you will receive the source file,
 * and in production (if there is a manifest.json), the compiled file.
 *
 * Compiled files must be located in `FCPATH`,
 * source paths are specified relative to `ROOTPATH`.
 *
 * Development: themes/default/css/app.scss > https://localhost:3000/themes/default/css/app.scss
 * Production:  themes/default/css/app.scss > https://example.com/assets/app-BolJ4eB_.css
 */
class Vite
{
    private string $webAssetsDir = 'assets/';
    private string $publicDir    = FCPATH;

    public function __construct(public array $manifest = [])
    {
        if (ENVIRONMENT === 'testing') {
            $this->publicDir = SUPPORTPATH . 'Libraries/Vite/';
        }
    }

    public function withWebAssetsDir(string $relativePath): self
    {
        $this->webAssetsDir = trim($relativePath, '/') . '/';

        return $this;
    }

    /**
     * Given an array of resource paths, will return the HTML tags to load them.
     */
    public function links(array $paths): string
    {
        $html = '';
        $paths = array_unique($paths);

        foreach ($paths as $path) {
            $html .= $this->link($path);
        }

        // If not in production environment, load the client from the Vite development server.
        if ($html !== '' && env('VITE_SERVE')) {
            $html = '<script type="module" src="' . $this->url('@vite/client') . '"></script>' . PHP_EOL . $html;
        }

        $listTags = explode(PHP_EOL, $html);

        return implode(PHP_EOL, array_unique($listTags));
    }

    /**
     * Examine the path and return the appropriate HTML tag.
     */
    public function link(string $path): string
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        return match ($ext) {
            'js' => $this->script($path),
            'css', 'scss', 'sass' => $this->style($path),
            default => throw new InvalidArgumentException('Unknown file type: ' . $ext),
        };
    }

    /**
     * Return the HTML tag to load a JavaScript file.
     */
    public function script(string $path): string
    {
        $realPath = $this->resolvePath($path);

        if ($realPath === '') {
            return '';
        }

        $dependencies = $this->addDependencies($path);

        return  ($dependencies === '' ? '' : $dependencies)
            . '<script type="module" src="' . $this->url($realPath) . '"></script>' . PHP_EOL;
    }

    /**
     * Return the HTML tag to load a CSS file.
     */
    public function style(string $path): string
    {
        $path = $this->resolvePath($path);

        return $path === '' ? '' : '<link rel="stylesheet" href="' . $this->url($path) . '">' . PHP_EOL;
    }

    /**
     * Return the URL to the file.
     */
    private function url(string $path)
    {
        if (! env('VITE_SERVE')) {
            // @todo examine the manifest file to get the proper link.
            // @todo Check realpath file?
            // @see https://vitejs.dev/guide/backend-integration.html for manifest information
            return base_url($path);
        }

        $devServer = 'http://' . env('VITE_HOST', 'localhost') . ':' . env('VITE_PORT', '3000') . '/';

        return $devServer . $path;
    }

    /**
     * @return string Returns a path or an empty string in case of an error.
     */
    private function resolvePath(string $path): string
    {
        if ($this->manifest !== [] && ! env('VITE_SERVE')) {
            if (! array_key_exists($path, $this->manifest)) {
                log_message('error', sprintf('[VITE] The asset file "%s" was not found in manifest.json.', $path));

                return '';
            }

            $path = $this->webAssetsDir . $this->manifest[$path]['file'];

        }

        if (env('VITE_SERVE') === true) {
            if (! is_file(ROOTPATH . $path)) {
                log_message('error', sprintf('[VITE] The asset file "%s" was not found.', ROOTPATH . $path));

                return '';
            }

            return $path;
        }

        if (! is_file($this->publicDir . $path)) {
            log_message('error', sprintf('[VITE] The asset file "%s" was not found.', $this->publicDir . $path));

            return '';
        }

        return $path;
    }

    /**
     * JS files may have dependencies (styles, scripts).
     */
    private function addDependencies(string $path): string
    {
        if ($this->manifest === [] || env('VITE_SERVE') === true) {
            return '';
        }

        $html = '';

        if (isset($this->manifest[$path]['imports'])) {
            foreach ($this->manifest[$path]['imports'] as $scriptName) {
                $html .= $this->script($scriptName);
            }
        }

        if (isset($this->manifest[$path]['css'])) {
            foreach ($this->manifest[$path]['css'] as $styleName) {
                $html .= '<link rel="stylesheet" href="' . $this->url($this->webAssetsDir . $styleName) . '">' . PHP_EOL;
            }
        }

        return $html;
    }
}
