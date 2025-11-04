<?php

namespace App\Libraries\Modules;

use CodeIgniter\Autoloader\Autoloader;
use CodeIgniter\Config\Factories;
use Config\Autoload;

class ModuleBootstrapper
{
    /**
     * Register active module namespaces with the autoloader.
     */
    public static function registerAutoloaderNamespaces(): void
    {
        $manager = service('modules');
        $manager->discover();
        $modules = $manager->getEnabledModules();

        /** @var Autoloader $autoloader */
        $autoloader = service('autoloader');

        // Add each module's namespace to the autoloader
        foreach ($modules as $module) {
            $manifest = $manager->getModule($module);

            // Use the provided namespace if available
            if (isset($manifest['namespace'])) {
                $autoloader->addNamespace($manifest['namespace'], $manifest['path']);
                continue;
            }

            // Otherwise, use the module name as the namespace
            $autoloader->addNamespace('App\\' . ucfirst($module), APPPATH . 'Modules/' . ucfirst($module));
        }
    }
}
