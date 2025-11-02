<?php

namespace App\Libraries;

use InvalidArgumentException;

/**
 * This manages the modules and addons that are installed in the application,
 * including their dependencies, enabling/disabling, and caching.
 *
 * This manager is then used in the Admin area to allow enabling/disabling
 * modules via a settings page.
 *
 * This is also used in the routes, autoloader, etc to determine which modules
 * are enabled and should be loaded, as well as setup namespaces, etc.
 */
class ModuleManager
{
    private array $modules = [];
    private array $enabled = [];
    private const CACHE_KEY = 'modules_manifest';
    private const CACHE_DEPENDENTS_KEY = 'modules_dependents';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Discover modules in modules/ and addons/ directories
     */
    public function discover(): array
    {
        // Try to get from cache first
        $cache = cache();
        $modules = $cache->get(self::CACHE_KEY);

        if ($modules === null) {
            $modules = [];

            // Scan modules
            foreach (glob(ROOTPATH . 'modules/*/module.php') as $manifestPath) {
                $moduleName = basename(dirname($manifestPath));
                $manifest = require $manifestPath;
                $modules[strtolower($moduleName)] = $manifest;
            }

            // Scan addons/
            foreach (glob(ROOTPATH . 'addons/*/module.php') as $manifestPath) {
                $moduleName = basename(dirname($manifestPath));
                $manifest = require $manifestPath;
                $modules[strtolower($moduleName)] = $manifest;
            }

            // Cache the discovered modules
            $cache->save(self::CACHE_KEY, $modules, self::CACHE_TTL);
        }

        $this->modules = $modules;

        $this->detectCircularDependencies();

        // Load enabled modules from settings
        $this->enabled = setting('Modules.enabledModules') ?? [];

        // Filter out any enabled modules that don't exist
        $this->enabled = array_filter($this->enabled, fn ($m) => isset($modules[$m]));

        return $modules;
    }

    /**
     * Clear all caches (modules and dependents)
     */
    public function clearCache(): void
    {
        $cache = cache();
        $cache->delete(self::CACHE_KEY);
        $cache->delete(self::CACHE_DEPENDENTS_KEY);
        log_message('info', 'Module manager caches have been cleared.');
    }

    /**
     * Clear cache for a specific module's dependents
     */
    private function clearModuleCache(string $moduleName): void
    {
        $cache = cache();
        $cacheKey = self::CACHE_DEPENDENTS_KEY . '_' . $moduleName;
        $cache->delete($cacheKey);
    }

    /**
     * Enable a module and all of its dependencies
     */
    public function enable(string $moduleName): void
    {
        $moduleName = strtolower($moduleName);

        // Make sure the module exists
        if (! isset($this->modules[$moduleName])) {
            throw new InvalidArgumentException("Cannot enable module '{$moduleName}', it does not exist.");
        }

        // Enables a module via the settings addon
        if (in_array($moduleName, $this->enabled, true)) {
            return;
        }

        // Enable dependencies first
        $dependencies = $this->getDependencies($moduleName);
        foreach ($dependencies as $dependency) {
            if (! in_array($dependency, $this->enabled, true)) {
                $this->enabled[] = $dependency;
                log_message('info', "Dependency module '{$dependency}' has been auto-enabled.");
            }
        }

        $this->enabled[] = $moduleName;
        setting('Modules.enabledModules', $this->enabled);

        // Clear cache for this module and all its dependencies
        $this->clearModuleCache($moduleName);
        foreach ($dependencies as $dependency) {
            $this->clearModuleCache($dependency);
        }

        log_message('info', "Module '{$moduleName}' has been enabled.");
    }

    /**
     * Get all modules that depend on the given module (reverse dependencies)
     *
     * @return string[]
     */
    public function getDependents(string $moduleName): array
    {
        $moduleName = strtolower($moduleName);

        // Try to get from cache first
        $cache = cache();
        $cacheKey = self::CACHE_DEPENDENTS_KEY . '_' . $moduleName;
        $dependents = $cache->get($cacheKey);

        if ($dependents !== null) {
            return $dependents;
        }

        $dependents = [];

        foreach ($this->modules as $name => $manifest) {
            if ($name === $moduleName) {
                continue;
            }

            try {
                $dependencies = $this->getDependencies($name);
                if (in_array($moduleName, $dependencies, true)) {
                    $dependents[] = $name;
                }
            } catch (InvalidArgumentException) {
                // Skip modules with invalid dependencies
                log_message('warning', "Skipping module '{$name}' due to invalid dependencies.");
                continue;
            }
        }

        // Cache the dependents relationship
        $cache->save($cacheKey, $dependents, self::CACHE_TTL);

        return $dependents;
    }

    /**
     * Check for circular dependencies in the module configuration
     *
     * @throws InvalidArgumentException
     */
    public function detectCircularDependencies(): void
    {
        foreach ($this->modules as $moduleName => $manifest) {
            $this->checkForCircularDependency($moduleName, [], []);
        }
    }

    /**
     * Helper method to detect circular dependencies using depth-first search
     *
     * @param string[] $visited
     * @param string[] $recursionStack
     * @throws InvalidArgumentException
     */
    private function checkForCircularDependency(string $moduleName, array $visited, array $recursionStack): void
    {
        $moduleName = strtolower($moduleName);
        $visited[$moduleName] = true;
        $recursionStack[$moduleName] = true;

        $manifest = $this->modules[$moduleName] ?? null;
        if (! $manifest || ! isset($manifest['dependencies'])) {
            unset($recursionStack[$moduleName]);
            return;
        }

        $moduleDependencies = (array) $manifest['dependencies'];

        foreach ($moduleDependencies as $dependency) {
            $dependency = strtolower($dependency);

            if (! isset($visited[$dependency])) {
                $this->checkForCircularDependency($dependency, $visited, $recursionStack);
            } elseif (isset($recursionStack[$dependency])) {
                // Found a cycle
                throw new InvalidArgumentException(
                    "Circular dependency detected: module '{$moduleName}' depends on '{$dependency}' which creates a cycle."
                );
            }
        }

        unset($recursionStack[$moduleName]);
    }

    /**
     * Get all dependencies for a module, recursively
     *
     * @return string[]
     * @throws InvalidArgumentException
     */
    private function getDependencies(string $moduleName, int $depth = 0): array
    {
        // Check recursion depth to prevent stack overflow
        if ($depth > 50) {
            throw new InvalidArgumentException(
                "Dependency depth limit exceeded for module '{$moduleName}'. Possible circular or deeply nested dependencies."
            );
        }

        $dependencies = [];
        $manifest = $this->modules[$moduleName] ?? null;

        if (! $manifest || ! isset($manifest['dependencies'])) {
            return [];
        }

        $moduleDependencies = (array) $manifest['dependencies'];

        foreach ($moduleDependencies as $dependency) {
            $dependency = strtolower($dependency);

            // Check if dependency exists
            if (! isset($this->modules[$dependency])) {
                throw new InvalidArgumentException(
                    "Module '{$moduleName}' requires dependency '{$dependency}', but it does not exist."
                );
            }

            // Add dependency if not already added
            if (! in_array($dependency, $dependencies, true)) {
                $dependencies[] = $dependency;

                // Recursively add sub-dependencies
                $subDependencies = $this->getDependencies($dependency, $depth + 1);
                foreach ($subDependencies as $subDependency) {
                    if (! in_array($subDependency, $dependencies, true)) {
                        $dependencies[] = $subDependency;
                    }
                }
            }
        }

        return $dependencies;
    }

    /**
     * Get all dependencies for a module (public accessor)
     *
     * @return string[]
     * @throws InvalidArgumentException
     */
    public function getModuleDependencies(string $moduleName): array
    {
        $moduleName = strtolower($moduleName);

        if (! isset($this->modules[$moduleName])) {
            throw new InvalidArgumentException("Module '{$moduleName}' does not exist.");
        }

        return $this->getDependencies($moduleName);
    }

    /**
     * Disable a module
     */
    public function disable(string $moduleName): void
    {
        $moduleName = strtolower($moduleName);

        // Make sure the module exists
        if (! isset($this->modules[$moduleName])) {
            throw new InvalidArgumentException("Cannot disable module '{$moduleName}', it does not exist.");
        }

        // Check if any enabled modules depend on this module
        $enabledDependents = array_filter(
            $this->getDependents($moduleName),
            fn ($dependent) => in_array($dependent, $this->enabled, true)
        );

        if (! empty($enabledDependents)) {
            throw new InvalidArgumentException(
                "Cannot disable module '{$moduleName}'. The following enabled modules depend on it: " . implode(', ', $enabledDependents)
            );
        }

        // Disables a module via the settings addon
        $this->enabled = array_filter($this->enabled, fn ($m) => $m !== $moduleName);
        setting('Modules.enabledModules', $this->enabled);

        // Clear cache for this module and all modules that depend on it
        $this->clearModuleCache($moduleName);
        $dependents = $this->getDependents($moduleName);
        foreach ($dependents as $dependent) {
            $this->clearModuleCache($dependent);
        }

        log_message('info', "Module '{$moduleName}' has been disabled.");
    }


    /**
     * Returns the array of enabled modules names.
     */
    public function getEnabledModules(): array
    {
        return $this->enabled;
    }

    /**
     * Checks if a module is enabled.
     */
    public function isEnabled(string $moduleName): bool
    {
        $moduleName = strtolower($moduleName);

        return in_array($moduleName, $this->enabled, true);
    }

    /**
     * Returns the array of all discovered modules.
     */
    public function getAllModules(): array
    {
        return $this->modules;
    }

    /**
     * Returns the manifest array for a specific module.
     */
    public function getModule(string $moduleName): ?array
    {
        $moduleName = strtolower($moduleName);

        return $this->modules[$moduleName] ?? null;
    }
}
