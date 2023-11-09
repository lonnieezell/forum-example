<?php

namespace App\Libraries;

use InvalidArgumentException;

class Vite
{
    public function __construct(private array $manifest = [])
    {
        // $this->manifest = json_decode(file_get_contents(ROOTPATH . 'public/build/manifest.json'), true);
    }

    /**
     * Given an array of resource paths, will return the HTML tags to load them.
     */
    public function links(array $paths): string
    {
        $html = '';

        // If not in production environment, load the client from the Vite development server.
        if (env('VITE_SERVE')) {
            $html .= '<script type="module" src="' . $this->url('@vite/client') . '"></script>';
        }

        foreach ($paths as $path) {
            $html .= "\n" . $this->link($path);
        }

        return $html;
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
        $path = $this->manifest[$path] ?? $path;

        return '<script type="module" src="' . $this->url($path) . '"></script>';
    }

    /**
     * Return the HTML tag to load a CSS file.
     */
    public function style(string $path): string
    {
        $path = $this->manifest[$path] ?? $path;

        return '<link rel="stylesheet" href="' . $this->url($path) . '">';
    }

    /**
     * Return the URL to the file.
     */
    private function url(string $path)
    {
        if (env('VITE_SERVE') === false) {
            // @todo examine the manifest file to get the proper link.
            // @see https://vitejs.dev/guide/backend-integration.html for manifest information
            return base_url($path);
        }

        $devServer = 'http://' . env('VITE_HOST', 'localhost') . ':' . env('VITE_PORT', '3000') . '/';

        return $devServer . $path;
    }
}
