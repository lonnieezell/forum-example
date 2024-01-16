<?php

namespace Config;

use App\Libraries\Alerts;
use App\Libraries\Policies\Policy;
use App\Libraries\Storage;
use App\Libraries\Theme;
use App\Libraries\View;
use App\Libraries\Vite;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Shield\Authentication\Passwords;
use CodeIgniter\Shield\Config\Auth;
use Config\View as ViewConfig;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    public static function policy($getShared = true): Policy
    {
        if ($getShared) {
            return static::getSharedInstance('policy');
        }

        return new Policy();
    }

    public static function alerts($getShared = true): Alerts
    {
        if ($getShared) {
            return static::getSharedInstance('alerts');
        }

        return new Alerts(config(Forum::class), static::session());
    }

    public static function vite($getShared = true): Vite
    {
        if ($getShared) {
            return static::getSharedInstance('vite');
        }

        return new Vite();
    }

    public static function storage($getShared = true): Storage
    {
        if ($getShared) {
            return static::getSharedInstance('storage');
        }

        return new Storage(new Filesystems());
    }

    /**
     * Override Shield's Password utilities to allow using setting() helper.
     */
    public static function passwords(bool $getShared = true): Passwords
    {
        if ($getShared) {
            return self::getSharedInstance('passwords');
        }

        /** @var Auth $config */
        $config = config('Auth');

        if ($options = setting('Auth.allowRegistration')) {
            $config->allowRegistration = $options;
        }
        if ($options = setting('Auth.passwordValidators')) {
            $config->passwordValidators = $options;
        }
        if ($options = setting('Auth.actions')) {
            $config->actions = $options;
        }
        if ($options = setting('Auth.sessionConfig')) {
            $config->sessionConfig = $options;
        }

        return new Passwords($config);
    }

    /**
     * Use our custom App\Libraries\View class instead of the default
     * to provide additional functionality.
     */
    public static function renderer(?string $viewPath = null, ?ViewConfig $config = null, bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('renderer', $viewPath, $config);
        }

        $viewPath = $viewPath ?: (new Paths())->viewDirectory;
        $config ??= config(ViewConfig::class);

        return new View($config, $viewPath, service('locator'), CI_DEBUG, service('logger'));
    }

    /**
     * Returns the Theme library.
     */
    public static function theme(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('theme');
        }

        return new Theme();
    }
}
