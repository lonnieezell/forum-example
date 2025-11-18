# Phase 1 Implementation Guide: Core Module Infrastructure

**Status**: Starting Phase 1
**Duration**: 3 Weeks
**Goal**: Establish modular architecture foundation for all future modules (core and addons)

---

## Overview

Phase 1 is about **refactoring the core application into independent modules** while maintaining all existing functionality. We're not building addon support yet—we're building the infrastructure that both core and addons will use.

By the end of Phase 1:
- Core features will be in `modules/` directory
- Each module will be independently loadable
- Modules communicate via hooks, not direct coupling
- All original functionality preserved
- Zero performance impact
- Foundation ready for addon system in Phase 2

---

## Phase 1 Breakdown

### Week 1: Module System Foundation (Days 1-5)

**Objective**: Build the core infrastructure for modules to exist

#### Day 1: ModuleManager Class

Create `app/Modules/ModuleManager.php`:

```php
namespace App\Modules;

class ModuleManager
{
    private array $modules = [];
    private array $enabled = [];
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Discover modules in modules/ and addons/ directories
     */
    public function discover(): array
    {
        $modules = [];

        // Scan modules/
        foreach (glob(ROOTPATH . 'modules/*/module.php') as $manifestPath) {
            $moduleName = basename(dirname($manifestPath));
            $manifest = require $manifestPath;
            $modules[$moduleName] = $manifest;
        }

        // Scan addons/
        foreach (glob(ROOTPATH . 'addons/*/module.php') as $manifestPath) {
            $moduleName = basename(dirname($manifestPath));
            $manifest = require $manifestPath;
            $modules[$moduleName] = $manifest;
        }

        $this->modules = $modules;
        return $modules;
    }

    /**
     * Load enabled modules in dependency order
     */
    public function load(): void
    {
        $this->discover();

        $enabled = config('Modules')->enabled ?? [];

        // Load only enabled modules
        foreach ($enabled as $name) {
            if (!isset($this->modules[$name])) {
                continue;
            }

            $manifest = $this->modules[$name];
            $this->loadModule($name, $manifest);
        }
    }

    private function loadModule(string $name, array $manifest): void
    {
        $providerClass = $manifest['provider'] ?? null;
        if (!$providerClass) {
            throw new \Exception("Module {$name} missing 'provider' in manifest");
        }

        if (!class_exists($providerClass)) {
            throw new \Exception("Provider class {$providerClass} not found for module {$name}");
        }

        $provider = new $providerClass($name, ROOTPATH . 'modules/' . $name, $this->container);
        $provider->register();
        $this->enabled[$name] = $provider;
    }

    public function getModule(string $name): ?ModuleProvider
    {
        return $this->enabled[$name] ?? null;
    }

    public function getAll(): array
    {
        return $this->modules;
    }

    public function getEnabled(): array
    {
        return $this->enabled;
    }

    public function isEnabled(string $name): bool
    {
        return isset($this->enabled[$name]);
    }

    public function enable(string $name): void
    {
        $config = config('Modules');
        $enabled = $config->enabled ?? [];
        $enabled[] = $name;
        $config->enabled = $enabled;
    }

    public function disable(string $name): void
    {
        $config = config('Modules');
        $enabled = $config->enabled ?? [];
        $key = array_search($name, $enabled);
        if ($key !== false) {
            unset($enabled[$key]);
        }
        $config->enabled = $enabled;
    }
}
```

Create `app/Config/Modules.php`:

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Modules extends BaseConfig
{
    /**
     * List of enabled modules
     * Core modules should be enabled by default
     */
    public array $enabled = [
        'core-discussions',
        'core-posts',
        'core-categories',
        'core-moderation',
        // Optional modules can be added here
    ];
}
```

#### Day 2: ModuleProvider Abstract Class

Create `app/Modules/ModuleProvider.php`:

```php
<?php

namespace App\Modules;

use CodeIgniter\Container\Container;
use CodeIgniter\Router\RouteCollection;

abstract class ModuleProvider
{
    protected string $moduleName;
    protected string $modulePath;
    protected Container $container;

    public function __construct(string $name, string $path, Container $container)
    {
        $this->moduleName = $name;
        $this->modulePath = $path;
        $this->container = $container;
    }

    /**
     * Register services in DI container
     * Called before boot()
     */
    abstract public function register(): void;

    /**
     * Bootstrap module after all services registered
     * Can use other modules' services
     */
    public function boot(): void
    {
    }

    /**
     * Return module manifest
     */
    abstract public function manifest(): array;

    /**
     * Register module routes
     */
    public function registerRoutes(RouteCollection $routes): void
    {
    }

    /**
     * Get module's permissions
     */
    public function registerPermissions(): array
    {
        return [];
    }

    /**
     * Get menu items from module
     */
    public function registerMenuItems(): array
    {
        return [];
    }

    /**
     * Get widgets from module
     */
    public function registerWidgets(): array
    {
        return [];
    }

    /**
     * Module's hook listeners
     */
    public function registerHooks(): array
    {
        return [];
    }

    /**
     * Validate module can be loaded
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * Get module path
     */
    protected function getModulePath(string $path = ''): string
    {
        return $this->modulePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get module public URL
     */
    protected function getModuleUrl(string $path = ''): string
    {
        $name = str_replace('core-', '', $this->moduleName);
        return base_url('modules/' . $name . ($path ? '/' . $path : $path));
    }
}
```

#### Day 3: Update Bootstrap

Update `public/index.php` to load modules:

```php
<?php
// ... existing CI bootstrap code ...

// Load and boot modules
$moduleManager = $container->make(\App\Modules\ModuleManager::class);
$moduleManager->discover();
$moduleManager->load();

// Boot all modules
foreach ($moduleManager->getEnabled() as $provider) {
    $provider->boot();
}

// ... rest of bootstrap ...
```

#### Day 4: Create Hook System Foundation

Create `app/Modules/HookManager.php`:

```php
<?php

namespace App\Modules;

use CodeIgniter\Events\Events;

class HookManager extends Events
{
    /**
     * Trigger an action hook
     */
    public static function trigger(string $hook, ...$args): void
    {
        Events::trigger($hook, ...$args);
    }

    /**
     * Apply filter hook to value
     */
    public static function filter(string $hook, $value, ...$args)
    {
        return Events::trigger($hook, $value, ...$args) ?? $value;
    }

    /**
     * Register action hook listener
     */
    public static function listen(string $hook, callable $callback): void
    {
        Events::on($hook, $callback);
    }

    /**
     * Register filter hook
     */
    public static function addFilter(string $hook, callable $callback): void
    {
        Events::on($hook, $callback);
    }
}
```

Create helper functions in `app/Helpers/HookHelper.php`:

```php
<?php

use App\Modules\HookManager;

if (!function_exists('fire_hook')) {
    /**
     * Trigger an action hook
     */
    function fire_hook(string $hook, ...$args): void
    {
        HookManager::trigger($hook, ...$args);
    }
}

if (!function_exists('apply_filter')) {
    /**
     * Apply filter hook to value
     */
    function apply_filter(string $hook, $value, ...$args)
    {
        return HookManager::filter($hook, $value, ...$args);
    }
}

if (!function_exists('add_hook')) {
    /**
     * Register hook listener
     */
    function add_hook(string $hook, callable $callback): void
    {
        HookManager::listen($hook, $callback);
    }
}
```

Load helper in `app/Config/Autoload.php`:
```php
public array $helpers = [
    'hook',
    // ... others
];
```

#### Day 5: Validate Structure

- [ ] ModuleManager discovers modules correctly
- [ ] ModuleProvider works as base class
- [ ] Config/Modules.php loads
- [ ] Bootstrap registers modules
- [ ] Existing app still works (no modules loaded yet)

---

### Week 2: Migrate Core Features to Modules (Days 6-10)

**Objective**: Move core discussion, post, category, moderation features into modules

This requires careful refactoring. Do one module at a time, test each step.

#### Day 6: Create `modules/core-discussions/`

Create directory structure:
```
modules/core-discussions/
├── module.php
├── DiscussionsProvider.php
├── config/
│   ├── Routes.php
│   └── Hooks.php
├── src/
│   ├── Controllers/
│   │   ├── DiscussionController.php
│   │   └── CategoryController.php
│   ├── Models/
│   │   ├── ThreadModel.php
│   │   ├── CategoryModel.php
│   ├── Entities/
│   │   ├── Thread.php
│   │   └── Category.php
│   ├── Services/
│   │   └── DiscussionService.php
│   └── Listeners/
│       └── DiscussionEventListener.php
├── views/
│   ├── discussions/
│   └── components/
├── database/
│   ├── migrations/
│   └── seeds/
└── README.md
```

Create `module.php`:
```php
<?php

return [
    'name'          => 'core-discussions',
    'label'         => 'Discussions',
    'version'       => '1.0.0',
    'description'   => 'Core discussion and thread functionality',
    'author'        => 'Forum Team',
    'type'          => 'core',
    'required'      => true,

    'provider'      => 'App\Modules\CoreDiscussions\DiscussionsProvider',
    'namespace'     => 'App\Modules\CoreDiscussions',

    'min_php'       => '8.4',
    'min_framework' => '4.5',
    'dependencies'  => [],

    'permissions'   => [
        'discussions.create' => 'Create discussions',
        'discussions.view'   => 'View discussions',
    ],
];
```

Create `DiscussionsProvider.php`:
```php
<?php

namespace App\Modules\CoreDiscussions;

use App\Modules\ModuleProvider;
use CodeIgniter\Router\RouteCollection;

class DiscussionsProvider extends ModuleProvider
{
    public function manifest(): array
    {
        return require $this->getModulePath('module.php');
    }

    public function register(): void
    {
        // Register services
        $this->container->make(Services\DiscussionService::class);
    }

    public function boot(): void
    {
        // Register event listeners
        $listener = new Listeners\DiscussionEventListener();
        \CodeIgniter\Events\Events::on('post_created', [$listener, 'onPostCreated']);
    }

    public function registerRoutes(RouteCollection $routes): void
    {
        require $this->getModulePath('config/Routes.php');
    }

    public function registerPermissions(): array
    {
        return $this->manifest()['permissions'] ?? [];
    }
}
```

Move files from `app/Controllers/Discussions/` to `modules/core-discussions/src/Controllers/`

Update namespaces in all moved files from `App\Controllers\Discussions` to `App\Modules\CoreDiscussions\Controllers`

Update view paths to use module namespace: `core-discussions::`

#### Days 7-10: Repeat for Other Core Modules

- Day 7: `core-posts`
- Day 8: `core-categories`
- Day 9: `core-moderation`
- Day 10: Test and fix issues

For each module:
1. Create directory structure
2. Create `module.php` manifest
3. Create `ModuleProvider`
4. Move controllers, models, entities
5. Update namespaces
6. Update view paths
7. Create routes file
8. Test that module loads and works
9. Update `app/Routes.php` to load from modules
10. Update migrations

---

### Week 3: Hook System & Finalization (Days 11-15)

**Objective**: Add hook points throughout application, ensure modules communicate via hooks

#### Day 11-12: Add Hook Points

Add hook calls to views and controllers at appropriate points:

In views (examples):
```php
<?= fire_hook('navbar_before_items', auth()->user()) ?>
<!-- existing navbar items -->
<?= fire_hook('navbar_after_items', auth()->user()) ?>

<?= fire_hook('sidebar_before_content') ?>
<!-- existing sidebar -->
<?= fire_hook('sidebar_after_content') ?>

<?= fire_hook('thread_after_display', $thread) ?>

<?= fire_hook('post_after_display', $post) ?>
```

In controllers (examples):
```php
// After creating thread
fire_hook('discussion:thread_created', $thread);

// After creating post
fire_hook('posts:post_created', $post);

// In moderation action
fire_hook('moderation:action_taken', $report, $action);
```

#### Day 13: Module Communication via Hooks

Ensure modules that need to interact do so via hooks:

Example: When post is created, notifications module should be notified
```php
// In core-posts module
fire_hook('posts:post_created', $post);

// In core-notifications module (if enabled)
add_hook('posts:post_created', function($post) {
    // Send notifications
    service(NotificationService::class)->notifyPostCreated($post);
});
```

#### Day 14: Test All Modules

- [ ] Enable core-discussions only → works
- [ ] Enable core-discussions + core-posts → works
- [ ] Enable all core modules → works
- [ ] Disable core-posts → discussions still work
- [ ] Disable core-reactions → no errors (if optional)

#### Day 15: Documentation & Review

- [ ] Update this guide with actual implementation notes
- [ ] Create MODULES.md documenting each core module
- [ ] Test on clean database
- [ ] Verify migrations work
- [ ] Check performance (should be identical to before)
- [ ] Create branch/PR for review

---

## Testing Checklist

### Functionality Tests
- [ ] All existing features work as before
- [ ] Can create discussions
- [ ] Can create posts
- [ ] Can create categories
- [ ] Can moderate content
- [ ] Reactions work (if enabled)
- [ ] Tagging works (if enabled)
- [ ] Notifications work (if enabled)

### Module Tests
- [ ] ModuleManager discovers all modules
- [ ] Modules load in dependency order
- [ ] Can enable/disable modules
- [ ] Disabled modules don't load
- [ ] Required modules cannot be disabled
- [ ] Hooks are called at right times

### Performance Tests
- [ ] Homepage loads in < 200ms (same as before)
- [ ] Discussion list loads in < 200ms
- [ ] Thread view loads in < 200ms
- [ ] No memory leaks with modules loaded

### Code Quality
- [ ] No direct module-to-module dependencies
- [ ] All public APIs documented
- [ ] Helper functions created for common operations
- [ ] Error handling is appropriate

---

## Rollback Plan

If something goes wrong:

1. **Before Migration**: Create git branch `phase-1-refactor`
2. **During Migration**: Commit working state after each module
3. **If Issues**:
   - Can rollback to before module migration
   - Or disable problematic module
   - Or revert single module refactor

**Rollback Steps**:
```bash
# If stuck
git checkout main
git reset --hard HEAD~N  # N = number of commits to go back

# Or, disable module in Config/Modules.php
public array $enabled = [
    'core-discussions',
    // 'core-posts',  // ← Disable this module
    'core-categories',
];
```

---

## Success Metrics for Phase 1

When Phase 1 is complete:

- ✅ All core features in `modules/` directory
- ✅ No direct module-to-module coupling
- ✅ All communication via hooks
- ✅ All original functionality preserved
- ✅ Can enable/disable optional modules
- ✅ Modules load in correct dependency order
- ✅ Zero performance impact
- ✅ Ready for Phase 2 (addon system)

---

## Next Steps (Phase 2)

After Phase 1 is complete and tested:

1. ModuleManager will also look in `addons/` directory
2. Addon modules work identically to core modules
3. Same hook system, menu system, widget system
4. Admin interface for managing all modules
5. Documentation for addon developers

---

## Questions During Implementation?

Refer to:
1. ADDON_SYSTEM_PRD.md - Overall architecture and requirements
2. This guide - Specific Phase 1 implementation steps
3. Example code sections above
4. Existing code for patterns to follow

Start with Day 1 tasks and work through sequentially. Each day should be tested before moving to next day.
