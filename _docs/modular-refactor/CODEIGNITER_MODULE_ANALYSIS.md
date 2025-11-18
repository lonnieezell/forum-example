# CodeIgniter 4 Module System Analysis
## What's Built-In vs What We Need to Build

**Date**: November 2, 2025
**Question**: How much can CodeIgniter handle natively vs custom implementation?

---

## TL;DR

**CodeIgniter Provides** (~40% of what we need):
- ✅ Module discovery and autoloading
- ✅ Auto-discovery of routes, services, registrars
- ✅ ServiceProvider pattern
- ✅ Namespace-based organization

**We Must Build** (~60% of what we need):
- ❌ Module dependency resolution
- ❌ Enable/disable functionality
- ❌ Multi-site configuration loading
- ❌ Module lifecycle management (bootstrap, shutdown)
- ❌ Multi-entry point support

---

## What CodeIgniter 4 Provides

### 1. Built-In Module System

**Location**: `Config/Modules.php` (already exists in your project)

```php
class Modules extends BaseModules {
    public $enabled = true;                    // Enable auto-discovery
    public $discoverInComposer = true;         // Composer packages
    public $aliases = [
        'events',      // Auto-discover Events in modules
        'filters',     // Auto-discover Filters in modules
        'registrars',  // Auto-discover Registrars in modules
        'routes',      // Auto-discover Routes in modules
        'services',    // Auto-discover ServiceProviders in modules
    ];
}
```

**What It Does**:
- Scans `modules/` directory for module structure
- Auto-discovers files matching `$aliases`
- Loads service providers automatically
- Registers routes from `Routes.php` in each module
- Loads events/filters/services per module

**How It Works**:
```
modules/
├── core-discussions/
│   ├── Config/
│   │   └── Routes.php          ← CodeIgniter auto-discovers this
│   ├── src/
│   │   └── Models/...
│   └── ModuleServiceProvider.php ← CodeIgniter auto-discovers this
├── core-moderation/
│   ├── Config/Routes.php
│   └── ModuleServiceProvider.php
└── core-reactions/
    ├── Config/Routes.php
    └── ModuleServiceProvider.php
```

**Current Config Pattern**:
```php
// Your current Modules.php already has:
public $aliases = [
    'events',        ← Auto-load Event classes from modules
    'filters',       ← Auto-load Filter classes from modules
    'registrars',    ← Auto-load Registrar classes from modules
    'routes',        ← Auto-load Routes.php files from modules
    'services',      ← Auto-load ServiceProvider classes from modules
];
```

---

### 2. ServiceProvider Pattern

**What It Is**: CodeIgniter's built-in module bootstrapping mechanism

**Current Project**: Using `ModuleServiceProvider.php` format

**How It Works**:
```php
// modules/core-discussions/ModuleServiceProvider.php
namespace Modules\CoreDiscussions;

use CodeIgniter\Module\ModuleServiceProvider as BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    // Automatically called during bootstrap
    public function register()
    {
        // Register services into container
        $this->container->set('ThreadModel', new ThreadModel());
    }

    // Automatically called after all services registered
    public function boot()
    {
        // Listen to framework events
        Events::on('framework:initialized', [$this, 'initModule']);
    }
}
```

**CodeIgniter Handles**:
- ✅ Auto-discovering ServiceProviders
- ✅ Calling `register()` in correct order
- ✅ Calling `boot()` after all services registered
- ✅ Dependency injection into methods

---

### 3. Auto-Discovery System

**What It Auto-Discovers**:

| Discovery | Location | Behavior |
|-----------|----------|----------|
| **Routes** | `modules/*/Config/Routes.php` | Merged into main route collection |
| **Services** | `modules/*/Config/Services.php` | Methods become container services |
| **Events** | `modules/*/src/Events/*` | Auto-registered event handlers |
| **Filters** | `modules/*/src/Filters/*` | Auto-registered HTTP filters |
| **Registrars** | `modules/*/src/Registrars/*` | Custom discovery/registration |

**Example: Route Auto-Discovery**
```php
// modules/core-discussions/Config/Routes.php
$routes->group('discussions', ['namespace' => 'Modules\CoreDiscussions\Controllers'], function($routes) {
    $routes->get('', 'DiscussionController::list', ['as' => 'discussions']);
    $routes->post('new', 'ThreadController::create', ['as' => 'thread-create']);
});

// CodeIgniter automatically merges this into main routes collection
// You DON'T need to manually include it in app/Routes.php
```

---

### 4. Namespace Organization

**Current Setup**: CodeIgniter automatically handles this

```php
// Autoloader configuration (app/Config/Autoload.php)
public $psr4 = [
    'App'       => APPPATH,
    'Config'    => CONFIGPATH,
    'Modules'   => MODULEPATH,  // ← Discovers modules/ automatically
];
```

**Result**:
- `modules/core-discussions/src/Models/Thread.php` → `Modules\CoreDiscussions\Models\Thread`
- `modules/core-discussions/src/Controllers/DiscussionController.php` → `Modules\CoreDiscussions\Controllers\DiscussionController`

---

## What CodeIgniter DOES NOT Provide

### 1. ❌ Module Dependency Resolution

**The Problem**:
```php
// modules/core-reactions/ModuleServiceProvider.php
public function register()
{
    // ERROR: What if core-discussions isn't loaded yet?
    $threadModel = $this->container->get('ThreadModel');
}
```

**CodeIgniter Loads Modules in Directory Order**:
- If `core-reactions/` comes before `core-discussions/`, it fails
- No dependency resolver
- No "depends on" specification

**What We Must Build**:
```php
// Our custom ModuleManager
class ModuleManager
{
    // Parse module.php metadata
    // Build dependency graph
    // Load modules in correct order
    // Fail if dependencies missing
    // Allow optional modules
}
```

---

### 2. ❌ Enable/Disable Modules

**CodeIgniter Behavior**: All discovered modules are loaded automatically

**The Problem**:
```
Both sites load ALL modules in modules/ directory
├── core-discussions ✓ loaded
├── core-moderation ✓ loaded
├── core-reactions ✓ loaded
├── core-wiki ✗ should NOT load on forum site
├── core-maps ✗ should NOT load on forum site
└── core-characters ✗ should NOT load on forum site
```

**What We Must Build**:
```php
// Our custom config-driven loading
// config/forum.php
return ['modules' => ['core-discussions', 'core-moderation', 'core-reactions']];

// config/campaign.php
return ['modules' => ['core-discussions', 'core-moderation', ..., 'core-wiki', 'core-maps', 'core-characters']];

// Our ModuleManager only loads specified modules
```

---

### 3. ❌ Multi-Entry Point Support

**Current CodeIgniter**: Single `public/index.php` entry point

**What We Need**:
```
public/
├── index.php           ← Forum site
├── index-campaign.php  ← Campaign site
└── index-admin.php     ← Admin site (future)
```

**Each Entry Point**:
```php
// public/index.php (forum)
define('SITE_TYPE', 'forum');
require_once ROOTPATH . 'vendor/autoload.php';
require_once APPPATH . 'Config/Paths.php';

// public/index-campaign.php (campaign)
define('SITE_TYPE', 'campaign');
require_once ROOTPATH . 'vendor/autoload.php';
require_once APPPATH . 'Config/Paths.php';
```

**What We Must Build**:
- Custom bootstrap logic that reads `SITE_TYPE`
- Load site-specific config: `config/forum.php` vs `config/campaign.php`
- Pass config to ModuleManager

---

### 4. ❌ Module Metadata & Lifecycle

**CodeIgniter Provides**: Just the file structure

**What We Need**:
```php
// modules/core-discussions/module.php (custom - we create this)
return [
    'name'          => 'core-discussions',
    'version'       => '1.0.0',
    'description'   => 'Forum discussions, threads, posts',
    'provider'      => 'Modules\CoreDiscussions\ModuleServiceProvider',
    'required'      => true,           // Must load
    'depends_on'    => [],             // No dependencies
    'provides'      => [               // What other modules need
        'ThreadModel',
        'PostModel',
        'DiscussionHooks',
    ],
    'events'        => [               // Events it fires
        'discussion:thread_created',
        'discussion:post_created',
    ],
];

// modules/core-reactions/module.php
return [
    'name'          => 'core-reactions',
    'version'       => '1.0.0',
    'description'   => 'Like/heart reactions',
    'provider'      => 'Modules\CoreReactions\ModuleServiceProvider',
    'required'      => false,          // Optional module
    'depends_on'    => ['core-discussions'],  // Needs discussions loaded first
    'provides'      => ['ReactionModel'],
];
```

---

### 5. ❌ Configuration-Driven Module Loading

**CodeIgniter**: Loads all modules discovered

**What We Need**:
```php
// config/forum.php
return [
    'site_name' => 'Forum',
    'theme' => 'forum',
    'modules' => [
        'core-discussions',
        'core-moderation',
        'core-reactions',
        'core-tagging',
        'core-notifications',
        'core-user-profiles',
        // campaign modules NOT in list
    ],
];

// config/campaign.php
return [
    'site_name' => 'Campaign Hub',
    'theme' => 'campaign',
    'modules' => [
        'core-discussions',
        'core-moderation',
        'core-reactions',
        'core-tagging',
        'core-notifications',
        'core-user-profiles',
        // PLUS campaign modules
        'core-campaigns',
        'core-wiki',
        'core-maps',
        'core-characters',
        'core-sessions',
    ],
];
```

---

## Our Implementation Strategy

### Layer 1: Leverage CodeIgniter (Minimal Customization)

**Keep CodeIgniter's Built-In Features**:
1. ✅ Service discovery and auto-loading
2. ✅ ServiceProvider pattern
3. ✅ Namespace organization
4. ✅ Route auto-discovery (we'll use this!)
5. ✅ Event system

**How**:
- Each module has `Config/Routes.php` → CodeIgniter auto-includes
- Each module has `ModuleServiceProvider.php` → CodeIgniter auto-boots
- Use CodeIgniter's service container for DI

---

### Layer 2: Build Custom ModuleManager

**Create** (`app/Managers/ModuleManager.php`):
```php
class ModuleManager
{
    private $config;           // Site config (forum/campaign)
    private $modules = [];     // Loaded modules
    private $metadata = [];    // Module metadata

    public function __construct(array $siteConfig)
    {
        $this->config = $siteConfig;
    }

    // Parse module.php metadata files
    public function discover(): array
    {
        // Load each module's module.php
        // Return array of metadata
    }

    // Sort by dependencies
    public function resolveDependencies(): array
    {
        // core-discussions loads before core-reactions
        // core-reactions loads only if enabled in config
    }

    // Enable only modules in config
    public function filterEnabled(): array
    {
        // Filter to only modules in $this->config['modules']
    }

    // Tell CodeIgniter to load them
    public function load(): void
    {
        // Register with CodeIgniter's module system
        // Call ServiceProvider->register() in order
        // Call ServiceProvider->boot() after all
    }
}
```

---

### Layer 3: Custom Bootstrap Logic

**Create** (`app/Bootstrap/ModuleBootstrapper.php`):
```php
class ModuleBootstrapper
{
    public static function init(string $siteType): void
    {
        // 1. Load site config based on SITE_TYPE
        $config = config($siteType . 'Config');  // forum or campaign

        // 2. Create ModuleManager
        $manager = new ModuleManager($config);

        // 3. Discover, resolve dependencies, filter enabled
        $manager->discover();
        $manager->resolveDependencies();
        $manager->filterEnabled();

        // 4. Load modules (register services, boot providers)
        $manager->load();
    }
}
```

---

### Layer 4: Multi-Entry Points

**Create** (`public/index-campaign.php`):
```php
<?php
define('SITE_TYPE', 'campaign');
require_once ROOTPATH . 'vendor/autoload.php';
require_once APPPATH . 'Config/Paths.php';

// Use the exact same bootstrap as index.php, but with different SITE_TYPE
```

---

## Code Changes Required

### What We Can Keep (No Changes)

```php
// app/Config/Modules.php - KEEP AS IS
// CodeIgniter handles auto-discovery perfectly
public $aliases = [
    'events',
    'filters',
    'registrars',
    'routes',      // This works great for us
    'services',
];
```

### What We Must Create

| File | Purpose | Complexity |
|------|---------|-----------|
| `app/Managers/ModuleManager.php` | Discover, resolve, enable/disable modules | Medium |
| `app/Bootstrap/ModuleBootstrapper.php` | Initialize modules per site | Low |
| `config/ForumConfig.php` | Forum site module list | Low |
| `config/CampaignConfig.php` | Campaign site module list | Low |
| `modules/core-discussions/module.php` | Module metadata | Low |
| `modules/core-moderation/module.php` | Module metadata | Low |
| ... (one per module) | Module metadata | Low |
| `public/index-campaign.php` | Campaign entry point | Low |

### What We Replace

```php
// REPLACE: app/Config/Modules.php
// Don't do anything - CodeIgniter handles this

// REPLACE: app/Routes.php
// Make it delegate to ModuleManager
// Instead of hardcoded routes, let modules provide them
$routes->get('/', static fn () => redirect()->to('/discussions'));
// (keep only global routes here, module routes come from modules/*/Config/Routes.php)

// REPLACE: public/index.php
// Add ModuleBootstrapper call
require_once APPPATH . 'Bootstrap/ModuleBootstrapper.php';
ModuleBootstrapper::init(SITE_TYPE ?? 'forum');
```

---

## Dependency Resolution Algorithm

**Challenge**: Module A depends on Module B, but we load them in arbitrary order

**Solution**: Topological sort

```php
// modules/core-reactions/module.php
'depends_on' => ['core-discussions'],

// ModuleManager algorithm:
1. Build dependency graph
   core-discussions → [] (no dependencies)
   core-reactions → [core-discussions]
   core-notifications → [core-discussions]

2. Topological sort (depth-first)
   Load core-discussions first
   Then load core-reactions
   Then load core-notifications

3. Error if circular dependency or missing dependency
   'core-reactions depends on missing module: core-discussions'
```

---

## Enable/Disable Strategy

**Current**:
```php
// All modules auto-load
modules/
├── core-discussions/
├── core-moderation/
├── core-reactions/
├── core-wiki/          ← Loaded on forum site (wrong!)
└── core-maps/          ← Loaded on forum site (wrong!)
```

**After ModuleManager**:
```php
// config/forum.php
'modules' => [
    'core-discussions',
    'core-moderation',
    'core-reactions',
    // core-wiki NOT loaded
    // core-maps NOT loaded
]

// ModuleManager only registers ServiceProviders for modules in config
// CodeIgniter's auto-discovery respects this
```

---

## Timeline

### Phase 1A: Foundation (Weeks 1-3) - Minimal CodeIgniter Changes

**Week 1 - Foundation**
- Create `ModuleManager.php` (250 lines)
- Create `ModuleBootstrapper.php` (100 lines)
- Create `module.php` templates (50 lines × 8 modules = 400 lines)
- Total: ~750 lines custom code

**Week 2 - Module System**
- Implement dependency resolution
- Implement enable/disable filtering
- Test with 2-3 modules
- Debug CodeIgniter integration

**Week 3 - Entry Points**
- Create `public/index-campaign.php`
- Create site configs: `ForumConfig.php`, `CampaignConfig.php`
- Test both sites loading independently
- Verify route isolation

### Phase 1B: Refactoring (Weeks 4-6)

- Move existing controllers/models into modules/
- Let CodeIgniter's auto-discovery handle routes
- Minimal changes to app/Routes.php

---

## Custom Code Estimate

**Total New Code**: ~1,200-1,500 lines of PHP
- ModuleManager: 300 lines
- ModuleBootstrapper: 100 lines
- Module metadata files: 400 lines (8 modules × 50 lines)
- Site configs: 100 lines
- Entry point: 50 lines
- Tests: 400-500 lines

**Code We Don't Have to Write**:
- Route auto-discovery (~200 lines we'd write)
- Service registration (~150 lines we'd write)
- Event registration (~100 lines we'd write)
- Filter registration (~100 lines we'd write)

**Total Savings** from CodeIgniter: ~550 lines

---

## Critical Dependencies on CodeIgniter

### What Would Break If We Replace Module System

1. ❌ Route auto-discovery from `modules/*/Config/Routes.php`
2. ❌ Service auto-discovery
3. ❌ Event auto-discovery
4. ❌ ServiceProvider pattern

### What We MUST Keep Using CodeIgniter For

1. ✅ `Config/Modules.php` - enables auto-discovery
2. ✅ ServiceProvider for bootstrapping
3. ✅ Service container DI
4. ✅ Route registration
5. ✅ Event system

---

## Recommendation

**Don't Replace CodeIgniter's Module System** - Extend It

### Why:
1. CodeIgniter's discovery works perfectly for basic modules
2. ServiceProvider pattern is solid
3. All the hard parts (discovery, autoloading) are done
4. We only need to add dependency resolution & configuration

### How:
1. Use CodeIgniter's existing Modules config
2. Build ModuleManager on top of it
3. Leverage auto-discovery for routes/services/events
4. Custom layer just for dependency resolution & enabling/disabling

### Result:
- Minimal code (~1,200 lines)
- Leverages battle-tested framework code
- Easy to understand (CodeIgniter + our custom layer)
- Easy to maintain (clear separation)

---

## Quick Comparison: Build vs Integrate

| Aspect | Build from Scratch | Integrate CodeIgniter |
|--------|-------------------|----------------------|
| Lines of Code | 3,000-5,000 | 1,200-1,500 |
| Route Discovery | Write custom | Use built-in |
| Service Registration | Write custom | Use built-in |
| Event System | Write custom | Use built-in |
| Time to Build | 4-6 weeks | 2-3 weeks |
| Maintenance | Entirely ours | Mostly CodeIgniter's |
| Learning Curve | Steep | Gentle |
| Testability | Good | Excellent |
| Community Support | None | CodeIgniter docs |

---

## Next Steps

1. **Week 1**: Build `ModuleManager` with dependency resolution
2. **Week 1-2**: Create module metadata files
3. **Week 2**: Implement enable/disable filtering
4. **Week 3**: Create entry points and test both sites
5. **Week 4+**: Refactor existing code into modules

Ready to start Phase 1A? 🚀
