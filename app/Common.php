<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter4.github.io/CodeIgniter4/
 */

use CodeIgniter\Config\Factories;

if (! function_exists('policy')) {
    /**
     * A convenience method for accessing the Policy service.
     */
    function policy(string $permission, mixed ...$arguments): bool
    {
        return service('policy')->can($permission, ...$arguments);
    }
}

/**
 * Generates the URLs to the vite resources.
 */
function vite(string|array $path): string
{
    $vite = service('vite');

    if (! is_array($path)) {
        $path = [$path];
    }

    return $vite->links($path);
}

if (! function_exists('manager')) {
    /**
     * More simple way of getting manager instances from Factories
     */
    function manager(string $name, bool $getShared = true): mixed
    {
        return Factories::managers($name, ['getShared' => $getShared]);
    }
}
