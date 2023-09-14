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
if (! function_exists('policy')) {
    /**
     * A convenience method for accessing the Policy service.
     *
     * @return mixed
     */
    function policy(string $permission, mixed ...$arguments)
    {
        return service('policy')->check($permission, ...$arguments);
    }
}
