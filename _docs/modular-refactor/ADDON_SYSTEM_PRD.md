# Modular Forum Architecture & Addon System - Product Requirements Document

**Version:** 2.0
**Date:** November 2, 2025
**Status:** Planning - Phase 1 (Core Refactor)
**Project:** Forum Example - Modular Architecture with Extensibility

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Multi-Site Architecture](#multi-site-architecture)
3. [Objectives](#objectives)
4. [Architecture Philosophy](#architecture-philosophy)
5. [System Overview](#system-overview)
6. [Core Module Specifications](#core-module-specifications)
7. [Module System Infrastructure](#module-system-infrastructure)
8. [Module Structure & Layout](#module-structure--layout)
9. [Module Lifecycle & Bootstrapping](#module-lifecycle--bootstrapping)
10. [Hook System](#hook-system)
11. [Menu System](#menu-system)
12. [Widget System](#widget-system)
13. [Admin Interface](#admin-interface)
14. [API Reference](#api-reference)
15. [Development Guide](#development-guide)
16. [Implementation Roadmap](#implementation-roadmap)
17. [Success Criteria](#success-criteria)

---

## Executive Summary

The Forum Example application is being refactored to use a **modular monolith** architecture where all functionality—both core and third-party—is organized as independent modules communicating via well-defined interfaces (hooks, events, services).

This single codebase will be deployed as **two distinct applications**:

1. **Forum Site**: Public community discussion forum
2. **Campaign Hub**: TTRPG campaign management with play-by-post forums

The same core modules power both sites. Different configurations, themes, and addons customize each site for its specific purpose.

**Phase 1** refactors the core application into modules that can be individually loaded, tested, and maintained. **Phase 2** builds the addon system that allows third-party developers to create additional modules following the same patterns.

This approach provides:
- **Single Codebase, Multiple Sites**: Deploy to different domains with different configurations
- **Cleaner Architecture**: Self-contained, decoupled modules
- **Better Testability**: Independent module testing, multiple site configurations
- **Flexible Functionality**: Enable/disable modules as needed per site
- **Seamless Extensibility**: Addons use identical patterns as core modules
- **Scalable Foundation**: Can add more sites in future using same modules

---

---

## Multi-Site Architecture

### Overview

This codebase supports deployment as **multiple distinct applications** using the same underlying code:

**Site 1: Public Forum** (forum.example.com)
- General discussion forums
- Open, public topics
- General user profiles
- Simple moderation
- Modules: core-auth, core-discussions, core-categories, core-moderation, core-reactions, core-tagging, core-notifications, core-user-profiles
- Theme: forum-theme
- Addons: badge-system, reputation-system

**Site 2: Campaign Hub** (campaigns.example.com)
- TTRPG campaign management
- Campaign-specific private forums (play-by-post)
- Campaign wiki for world-building
- Maps and location management
- Character sheets and management
- Campaign scheduling and sessions
- Modules: [All from Forum] + core-campaigns, core-wiki, core-maps, core-characters, core-sessions
- Theme: campaign-theme
- Addons: campaign-notifications-addon, character-portrait-addon

### How It Works

```
Same Codebase
    ↓
Different Entry Points (index.php vs index-campaign.php)
    ↓
Different Configuration (config/forum.php vs config/campaign.php)
    ↓
Different Module Selection (which modules to load)
    ↓
Different Theme (forum-theme vs campaign-theme)
    ↓
Different Addons (site-specific addons)
    ↓
✓ Forum Site  |  ✓ Campaign Site
```

### Key Architecture Points

1. **Shared Core Modules**: Auth, discussions, posts, moderation, reactions, notifications, tagging, user profiles work identically on both sites
2. **Site-Specific Modules**: Campaign modules only load on campaign site
3. **Site-Aware Configuration**: `config/forum.php` and `config/campaign.php` specify which modules/addons load
4. **Theme-Based UI**: Same modules rendered differently via forum-theme vs campaign-theme
5. **Single Database**: Both sites share same database (with permissions controlling data isolation)
6. **Independent Evolution**: Each site can evolve independently while sharing core updates

### Detailed Architecture

See **MULTI_SITE_ARCHITECTURE.md** for complete specification:
- Project structure and directory layout
- Module categorization strategy
- Theme system for different UIs
- Entry points and deployment strategies
- Configuration approach
- Future expansion possibilities

---

## Objectives

### Phase 1: Core Refactoring (This Phase)

1. **Establish Module Infrastructure**: Build system for core modules to coexist with addon modules
2. **Extract Core Features**: Move forum core into independent modules (discussions, posts, categories, etc.)
3. **Implement Hook System**: Enable modules to communicate via hooks/events instead of direct coupling
4. **Create Foundation**: Build everything needed for addons to work identically to core modules

### Phase 2: Addon System (Future)

1. **Third-Party Extensibility**: Enable developers to create addons using same patterns as core
2. **Dynamic Loading**: Discover and load addons from `addons/` directory
3. **Admin Management**: UI to enable/disable/configure addons
4. **Complete Feature Set**: Hooks, menus, widgets, permissions, assets

### Primary Goals (Both Phases)

1. **Modularity**: Clear separation of concerns; modules don't depend on internal implementation
2. **Decoupling**: Modules communicate via hooks/events, not direct method calls
3. **Testability**: Each module can be tested independently
4. **Performance**: No overhead; lazy-load only what's needed
5. **Security**: Modules isolated; core integrity maintained
6. **DX**: Simple, intuitive APIs for module developers
7. **Flexibility**: Enable/disable modules without breaking application

### Success Metrics

- Core refactored into 6+ independent modules
- Zero direct coupling between modules (all via hooks)
- All module hooks documented with examples
- Can disable optional modules without crashing
- Module loading < 50ms overhead per module
- Module tests achieve 80%+ coverage
- Addons creation time < 1 hour

---

## Architecture Philosophy

### Modular Monolith Pattern

We're building a **modular monolith**—a single application deployed as one unit, but internally organized as independent, loosely-coupled modules. This provides benefits of microservices (separation, scalability, team ownership) without operational complexity.

**Key Principles**:

1. **Module Autonomy**: Each module owns its routes, models, views, migrations
2. **Explicit Dependencies**: Modules declare what they depend on
3. **Event-Driven Communication**: Modules communicate via hooks/events, not direct calls
4. **Clean Boundaries**: Module internal implementation is hidden
5. **Gradual Extraction**: Core can be refactored incrementally

### No Direct Coupling

❌ **WRONG**: Module A directly calls Module B's methods
```php
$postsModel = model('PostModel');
$posts = $postsModel->where('thread_id', $id)->findAll();
```

✅ **RIGHT**: Module A hooks into events Module B triggers
```php
Events::on('thread_loaded', function($thread) {
    $posts = service('PostService')->getForThread($thread->id);
});
```

---

## System Overview

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Application Core                         │
│  (Router, DI Container, Config, Bootstrap)                 │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                    Module Manager                            │
│  (Discovery, Loading, Dependency Resolution, Lifecycle)     │
└─────────────────────────────────────────────────────────────┘
                              ↓
    ┌────────────────────────────────────────────────┐
    │  Core Modules (in modules/)                    │
    ├────────────────────────────────────────────────┤
    │  • core-discussions (REQUIRED)                 │
    │  • core-moderation (REQUIRED)                  │
    │  • core-categories (REQUIRED)                  │
    │  • core-reactions (OPTIONAL)                   │
    │  • core-tagging (OPTIONAL)                     │
    │  • core-notifications (OPTIONAL)               │
    │  • core-user-profiles (OPTIONAL)               │
    │  • core-images (OPTIONAL)                      │
    └────────────────────────────────────────────────┘
                              ↓
    ┌────────────────────────────────────────────────┐
    │  Hook/Event System + Menu + Widget System      │
    │  (Inter-Module Communication)                  │
    └────────────────────────────────────────────────┘
                              ↓
    ┌────────────────────────────────────────────────┐
    │  Third-Party Addon Modules (in addons/)        │
    │  (Follow identical patterns as core modules)   │
    └────────────────────────────────────────────────┘
```

### Directory Structure

```
forum-example/
├── app/                          ← Minimal; mostly config
│   ├── Config/
│   │   ├── App.php
│   │   ├── Modules.php           ← NEW: Module configuration
│   │   └── Hooks.php             ← NEW: Hook definitions
│   ├── Bootstrap/
│   │   └── ModuleBootstrapper.php ← NEW: Load modules
│   └── Routes.php                ← Simple; delegates to modules
├── modules/                      ← NEW: Core modules
│   ├── core-discussions/         ← Core feature: Categories, threads, posts (REQUIRED)
│   │   ├── config/
│   │   ├── src/
│   │   ├── views/
│   │   ├── database/
│   │   └── ModuleProvider.php
│   ├── core-categories/          ← Core feature (REQUIRED)
│   ├── core-moderation/          ← Core feature (REQUIRED)
│   ├── core-reactions/           ← Optional feature
│   ├── core-tagging/             ← Optional feature
│   ├── core-notifications/       ← Optional feature
│   ├── core-user-profiles/       ← Optional feature
│   └── core-images/              ← Optional feature
├── addons/                       ← Third-party modules
│   ├── badge-system/
│   ├── reputation/
│   └── ...
└── public/
    ├── css/
    └── js/
```

---

## Core Module Specifications

### Required Core Modules (Cannot be disabled)

#### 1. `core-discussions`
**Responsibility**: Forum structure and discussion management (categories, threads, posts)
**Contains**:
- Category CRUD and hierarchy
- Thread creation, editing, deletion
- Post creation, editing, deletion
- Thread and post display/pagination
- Reply threading
- Category permissions

**Provides Events**:
- `discussion:thread_created`
- `discussion:thread_updated`
- `discussion:thread_deleted`
- `discussion:post_created`
- `discussion:post_updated`
- `discussion:post_deleted`
- `discussion:category_loaded`

**Depends On**: None

---

#### 2. `core-categories`
**Responsibility**: Category management and filtering
**Contains**:
- Category listing and search
- Category-specific thread filtering
- Breadcrumb generation

**Provides Events**:
- `categories:filtered`

**Depends On**: `core-discussions`

---

#### 3. `core-moderation`
**Responsibility**: Report handling and moderation actions
**Contains**:
- Report creation and display
- Moderation queue management
- Moderation actions (approve, deny, ignore)
- Moderation logs

**Provides Events**:
- `moderation:action_taken`
- `moderation:report_created`

**Depends On**: `core-discussions`

---

### Optional Core Modules (Can be disabled)

#### 4. `core-reactions`
**Responsibility**: Like, heart, emoji reactions to posts/threads
**Contains**:
- Reaction registration and display
- User reaction history

**Provides Events**:
- `reactions:added`
- `reactions:removed`

**Depends On**: `core-discussions`

---

#### 5. `core-tagging`
**Responsibility**: Tagging system for threads
**Contains**:
- Tag creation and management
- Thread tagging
- Tag-based filtering and search

**Provides Events**:
- `tagging:tag_added`
- `tagging:tag_removed`

**Depends On**: `core-discussions`

---

#### 6. `core-notifications`
**Responsibility**: User notifications for various events
**Contains**:
- Notification storage and display
- Email/in-app notifications
- Notification preferences

**Provides Events**:
- `notifications:sent`

**Depends On**: `core-discussions`

---

#### 7. `core-user-profiles`
**Responsibility**: User profile pages and customization
**Contains**:
- User profile display
- User statistics (posts, likes, etc.)
- Profile customization options

**Provides Events**:
- `profiles:viewed`
- `profiles:updated`

**Depends On**: None (can work standalone)

---

#### 8. `core-images`
**Responsibility**: Image upload, storage, and display
**Contains**:
- Image upload handling
- Image storage management
- Image display/resizing

**Provides Events**:
- `images:uploaded`
- `images:deleted`

**Depends On**: None (can work standalone)

---

## Module System Infrastructure

### Module Manager (NEW)

Central service for managing all modules (core and addons).

**Responsibilities**:
- Discover modules in `modules/` and `addons/` directories
- Load module manifests and validate
- Resolve module dependencies
- Bootstrap modules in correct order
- Manage module lifecycle (enable/disable/install/uninstall)
- Register module routes, services, permissions

**API**:
```php
class ModuleManager
{
    public function discover(): array;                    // Find all modules
    public function load(): void;                         // Load enabled modules
    public function getModule(string $name): ?Module;     // Get module
    public function isEnabled(string $name): bool;
    public function getAll(): array;                      // All modules
    public function getEnabled(): array;                  // Only enabled

    // Lifecycle
    public function enable(string $name): void;
    public function disable(string $name): void;
    public function install(string $name): void;
    public function uninstall(string $name): void;

    // Info
    public function getManifest(string $name): array;
    public function getVersion(string $name): string;
    public function getDependencies(string $name): array;
    public function validateDependencies(string $name): bool;
    public function getStatus(string $name): string;     // enabled, disabled, error
}
```

### Module Provider (Base Class)

Each module implements this to initialize itself.

```php
abstract class ModuleProvider
{
    protected string $moduleName;
    protected string $modulePath;
    protected Container $container;

    /**
     * Register services in the DI container
     * Called first, before boot()
     */
    abstract public function register(): void;

    /**
     * Bootstrap module after all services registered
     * Can use services from other modules
     */
    public function boot(): void {}

    /**
     * Return module manifest/metadata
     */
    abstract public function manifest(): array;

    /**
     * Register module routes
     */
    public function registerRoutes(RouteCollection $routes): void {}

    /**
     * Register module permissions
     */
    public function registerPermissions(): array { return []; }

    /**
     * Return menu items this module provides
     */
    public function registerMenuItems(): array { return []; }

    /**
     * Return widgets this module provides
     */
    public function registerWidgets(): array { return []; }

    /**
     * Return hooks this module provides/listens to
     */
    public function registerHooks(): array { return []; }

    /**
     * Publish module assets to public folder
     */
    public function publishAssets(): void {}

    /**
     * Validate module can be enabled (dependencies met)
     */
    public function validate(): bool { return true; }
}
```

### Module Manifest Format

Each module has a `module.php` file in its root:

```php
<?php

return [
    // Identification
    'name'              => 'core-discussions',
    'label'             => 'Discussions',
    'version'           => '1.0.0',
    'description'       => 'Core discussion and thread functionality',
    'author'            => 'Forum Team',

    // Location
    'provider'          => 'App\Modules\CoreDiscussions\DiscussionsProvider',
    'namespace'         => 'App\Modules\CoreDiscussions',

    // Classification
    'type'              => 'core',                         // 'core' or 'addon'
    'required'          => true,                          // Cannot disable
    'icon'              => 'fa-comments',                 // For admin UI

    // Compatibility
    'min_php'           => '8.4',
    'min_framework'     => '4.5',
    'dependencies'      => [],                            // Module names

    // Features
    'permissions'       => [
        'discussions.create' => 'Create discussions',
        'discussions.view' => 'View discussions',
    ],
    'routes'            => 'config/Routes.php',           // Path to routes file
    'migrations'        => 'database/migrations',         // Path to migrations
];
```

---

## Module Structure & Layout

### Directory Structure

**Core Module Layout** (in `modules/core-discussions/`):

```
modules/
├── core-discussions/
│   ├── module.php                      # Module manifest
│   ├── DiscussionsProvider.php         # Module bootstrap provider
│   ├── config/
│   │   ├── Routes.php                  # Module routes
│   │   └── Hooks.php                   # Hook definitions
│   ├── src/
│   │   ├── Controllers/
│   │   │   ├── DiscussionController.php
│   │   │   └── CategoryController.php
│   │   ├── Models/
│   │   │   ├── ThreadModel.php
│   │   │   └── CategoryModel.php
│   │   ├── Entities/
│   │   │   ├── Thread.php
│   │   │   └── Category.php
│   │   ├── Cells/
│   │   ├── Services/
│   │   │   └── DiscussionService.php
│   │   └── Listeners/
│   │       └── DiscussionEventListener.php
│   ├── views/
│   │   ├── discussions/
│   │   └── components/
│   ├── database/
│   │   ├── migrations/
│   │   │   └── 2024_01_01_000001_CreateThreadsTable.php
│   │   └── seeds/
│   ├── language/
│   │   └── en/
│   │       └── Discussions.php
│   ├── public/
│   │   ├── css/
│   │   └── js/
│   ├── tests/
│   │   ├── DiscussionControllerTest.php
│   │   └── ThreadModelTest.php
│   └── README.md
```

**Addon Module Layout** (in `addons/badge-system/`):

```
addons/
├── badge-system/
│   ├── module.php                      # Module manifest (identical structure)
│   ├── BadgeSystemProvider.php         # Module bootstrap provider
│   ├── config/
│   │   ├── Routes.php
│   │   └── Hooks.php
│   ├── src/
│   │   ├── Controllers/
│   │   ├── Models/
│   │   ├── Services/
│   │   └── Listeners/
│   ├── views/
│   ├── database/
│   │   └── migrations/
│   ├── public/
│   ├── tests/
│   └── README.md
```

### Namespace Convention

**Core Modules**: `App\Modules\{ModuleName}\`
- Controllers: `App\Modules\CoreDiscussions\Controllers\`
- Models: `App\Modules\CoreDiscussions\Models\`
- Services: `App\Modules\CoreDiscussions\Services\`

**Addon Modules**: `{Vendor}\{ModuleName}\`
- Controllers: `BadgeSystem\Controllers\`
- Models: `BadgeSystem\Models\`
- Services: `BadgeSystem\Services\`

---

## Module Lifecycle & Bootstrapping

### Discovery Phase

1. **Scan Directories**: Look for `modules/` and `addons/`
2. **Find Manifests**: Each module must have `module.php`
3. **Load Manifests**: Parse manifests for metadata
4. **Validate**: Check for required fields, proper format
5. **Register Available**: Add to available modules list (enabled or not)

### Dependency Resolution Phase

1. **Map Dependencies**: Build dependency graph
2. **Detect Cycles**: Error if circular dependencies
3. **Order Modules**: Determine load order (topological sort)
4. **Validate Requirements**: Check all dependencies are available

**Example Dependency Chain**:
```
core-discussions (no dependencies)
    ↓ needed by
core-posts (depends on core-discussions)
core-categories (depends on core-discussions)
core-reactions (depends on core-discussions, core-posts)
    ↓ needed by
core-notifications (depends on core-discussions, core-posts)
```

### Bootstrap Phase (Application Startup)

1. **Load Enabled Modules** in dependency order:
   ```
   ModuleManager→discover()
   ModuleManager→resolveOrder()
   ModuleManager→loadEnabled()
   ```

2. **For Each Module**:
   - Register services (`register()`)
   - Register routes
   - Register permissions
   - Register hooks
   - Register menu items
   - Register widgets

3. **Boot Phase** (all services registered):
   - Call `boot()` on each provider
   - Modules can now use services from other modules
   - Fire `module:booted` events

4. **Fire Global Events**:
   - `app:modules_loaded`
   - Addons listen here for initialization

### Enable/Disable Workflow

**To Enable Module**:
```
1. Check dependencies are enabled
2. If first time: run migrations
3. Set enabled flag in config
4. Fire module:enabled event
5. Clear cache
6. Suggest restart/reload
```

**To Disable Module**:
```
1. Fire module:disabling event (modules can cleanup)
2. Set disabled flag in config
3. Fire module:disabled event
4. Clear cache
5. Suggest restart/reload
```

### Install Workflow (New Module)

```
1. Validate module.php exists and valid
2. Run migrations for this module
3. Set enabled = true
4. Call provider→install() if exists
5. Fire module:installed event
```

### Uninstall Workflow

```
1. Fire module:uninstalling event
2. Run down migrations
3. Call provider→uninstall() if exists
4. Remove from modules list
5. Fire module:uninstalled event
```

### Hook Categories

#### 1. Content/UI Hooks

These hooks inject content into specific UI regions.

| Hook Name | Location | Parameters | Returns |
|-----------|----------|-----------|---------|
| `navbar_before_items` | Before navbar menu items | `$user` | HTML string |
| `navbar_after_items` | After navbar menu items | `$user` | HTML string |
| `navbar_user_menu_before` | Before user account dropdown items | `$user` | HTML string |
| `navbar_user_menu_after` | After user account dropdown items | `$user` | HTML string |
| `sidebar_before_content` | Top of sidebar | `$context` array | HTML string |
| `sidebar_after_content` | Bottom of sidebar | `$context` array | HTML string |
| `thread_header_after` | After thread title/meta | `$thread` | HTML string |
| `thread_action_bar_items` | Extend thread action bar | `$thread, $user` | Array of action items |
| `post_action_bar_items` | Extend post action bar | `$post, $user` | Array of action items |
| `post_content_after` | After post content | `$post` | HTML string |
| `user_profile_card_after` | After user profile card | `$user` | HTML string |
| `member_list_row_after` | After member row | `$user` | HTML string |
| `admin_dashboard_widgets` | Admin dashboard sections | `$user` | Array of widgets |
| `moderation_sidebar_after` | After moderation sidebar | none | HTML string |
| `footer_before_links` | Before footer links | none | HTML string |

#### 2. Data Filter Hooks

These hooks modify data before processing.

| Hook Name | Location | Parameters | Returns |
|-----------|----------|-----------|---------|
| `filter:thread_display_data` | Format thread for display | `$thread` | Modified thread |
| `filter:post_display_data` | Format post for display | `$post` | Modified post |
| `filter:user_profile_data` | Modify user profile display | `$user` | Modified user data |
| `filter:search_results` | Modify search results | `$results, $query` | Modified results |

#### 3. Lifecycle Hooks

These hooks fire during application/addon lifecycle events.

| Hook Name | Location | Parameters | Returns |
|-----------|----------|-----------|---------|
| `app_initialized` | After app bootstrap | none | void |
| `thread_before_create` | Before thread inserted | `$data` | Modified data |
| `thread_after_create` | After thread created | `$thread` | void |
| `post_before_create` | Before post inserted | `$data` | Modified data |
| `post_after_create` | After post created | `$post` | void |
| `user_before_register` | Before user registration | `$data` | Modified data |
| `user_after_register` | After user registered | `$user` | void |
| `addon_enabled` | Addon activated | `$addonName` | void |
| `addon_disabled` | Addon deactivated | `$addonName` | void |

### Hook API

#### Trigger Action Hook

```php
// Single callback
Events::trigger('thread_after_create', $thread);

// Multiple callbacks
$callbacks = Events::listeners('thread_after_create');
foreach ($callbacks as $callback) {
    $callback($thread);
}
```

#### Apply Filter Hook

```php
$data = [
    'title' => 'Thread Title',
    'content' => 'Thread content',
];

// Modify $data through filter hooks
$data = Events::filter('filter:thread_display_data', $data);
```

#### Register Hook in Addon

```php
// In AddonServiceProvider::boot()
Events::on('thread_after_create', function($thread) {
    // Do something with thread
    log_message('info', 'New thread: ' . $thread->title);
});

// In AddonServiceProvider::getHooks()
public function getHooks(): array
{
    return [
        'thread_after_create' => [ThredListener::class, 'handle'],
        'filter:user_profile_data' => [UserListener::class, 'filterProfileData'],
    ];
}
```

### Hook Documentation Template

Create `HOOKS.md` in addon:

```markdown
# Available Hooks

## Action Hooks

### my_addon_action_name
**Location**: Action name description
**Parameters**:
- `$param1` (Type): Description
- `$param2` (Type): Description

**Example**:
\`\`\`php
Events::on('my_addon_action_name', function($param1, $param2) {
    // Do something
});
\`\`\`

## Filter Hooks

### filter:my_addon_data
**Location**: Filter name description
**Parameters**:
- `$data` (Type): Data to be filtered

**Returns**: Modified $data

**Example**:
\`\`\`php
Events::on('filter:my_addon_data', function($data) {
    $data['custom_field'] = 'value';
    return $data;
});
\`\`\`
```

---

## Menu System

### Overview

The menu system allows addons to register menu items in:
- Primary navigation bar
- User account dropdown
- Admin panel sidebar
- Moderation panel sidebar
- Custom locations defined by theme

### Menu Item Structure

```php
[
    'name'      => 'addon-features',          // Unique identifier
    'label'     => 'Features',                // Display label
    'url'       => route('addon-features'),   // URL or route name
    'icon'      => 'fa-star',                 // Icon class
    'order'     => 10,                        // Sort order
    'active'    => url_is('features/*'),      // Active indicator
    'permission'=> 'addon.view',              // Required permission
    'children'  => [                          // Submenu items
        [
            'name'  => 'features-list',
            'label' => 'View Features',
            'url'   => route('addon-features.list'),
        ],
    ],
]
```

### Menu Manager API

```php
// Register menu items
manager(MenuManager::class)->register('navbar', 'addon-menu', [
    'label'  => 'My Addon',
    'url'    => 'addon-home',
    'icon'   => 'fa-puzzle-piece',
    'order'  => 50,
]);

// Register in specific location
manager(MenuManager::class)->register('user_dropdown', 'addon-settings', [
    'label'  => 'Addon Settings',
    'url'    => 'addon-settings',
    'permission' => 'addon.manage',
]);

// Get menu for rendering
$menu = manager(MenuManager::class)->get('navbar');

// Remove menu item
manager(MenuManager::class)->remove('navbar', 'addon-menu');

// Check if user can see menu item (with permissions)
if (manager(MenuManager::class)->canView('navbar', 'addon-menu')) {
    // Render menu item
}
```

### Available Menu Locations

1. **navbar**: Main navigation bar items (left of primary menu)
2. **navbar_user**: User account dropdown menu
3. **admin_sidebar**: Admin panel sidebar
4. **moderation_sidebar**: Moderation panel sidebar
5. **footer_links**: Footer quick links
6. **Custom locations**: Defined by addons/theme

### Menu Registration in ServiceProvider

```php
public function boot(): void
{
    $manager = manager(MenuManager::class);

    // Main navigation
    $manager->register('navbar', 'my-addon', [
        'label'  => 'My Addon',
        'url'    => route('addon-home'),
        'icon'   => 'fa-star',
        'order'  => 50,
        'children' => [
            [
                'name'  => 'my-addon-items',
                'label' => 'Items',
                'url'   => route('addon-items'),
            ],
            [
                'name'  => 'my-addon-settings',
                'label' => 'Settings',
                'url'   => route('addon-settings'),
                'permission' => 'addon.manage',
            ],
        ],
    ]);
}
```

### Menu Rendering in Views

```php
<!-- In theme view -->
<?= render_menu('navbar') ?>

<!-- With custom wrapper -->
<?php foreach (menu('navbar') as $item): ?>
    <li>
        <a href="<?= $item['url'] ?>"
           <?= $item['active'] ? 'class="active"' : '' ?>>
            <i class="<?= $item['icon'] ?>"></i>
            <?= $item['label'] ?>
        </a>
    </li>
<?php endforeach; ?>
```

---

## Widget System

### Overview

Widgets are reusable, configurable UI components that can be placed in defined regions of the application.

### Widget Types

1. **Static Regions**: Fixed locations for widgets (sidebar, footer, etc.)
2. **Dynamic Regions**: Custom locations defined by addons
3. **Admin-configurable**: Admin can enable/disable/order widgets

### Widget Base Class

```php
abstract class AbstractWidget
{
    protected string $id;
    protected string $title;
    protected string $description;
    protected array $settings = [];
    protected int $order = 0;
    protected bool $enabled = true;

    abstract public function render(): string;

    public function getId(): string { return $this->id; }

    public function getTitle(): string { return $this->title; }

    public function getDescription(): string { return $this->description; }

    public function getSetting(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    public function setSetting(string $key, $value): self
    {
        $this->settings[$key] = $value;
        return $this;
    }

    public function getSettingsForm(): array { return []; }

    public function validateSettings(array $settings): array { return []; }
}
```

### Widget Example

```php
namespace Example\Addon\Widgets;

use App\Addons\Widgets\AbstractWidget;

class StatsWidget extends AbstractWidget
{
    protected string $id = 'example-stats';
    protected string $title = 'Example Stats';
    protected string $description = 'Displays addon statistics';

    public function render(): string
    {
        $stats = [
            'items' => 42,
            'users' => 15,
        ];

        return view('example::widgets/stats', $stats);
    }

    public function getSettingsForm(): array
    {
        return [
            'show_details' => [
                'type' => 'checkbox',
                'label' => 'Show detailed stats',
                'default' => true,
            ],
            'items_limit' => [
                'type' => 'number',
                'label' => 'Items to display',
                'default' => 10,
            ],
        ];
    }
}
```

### Widget Registration

```php
// In AddonServiceProvider::boot()
manager(WidgetManager::class)->register('example-stats',
    Example\Addon\Widgets\StatsWidget::class
);

// Register in region
manager(RegionManager::class)->addWidget('sidebar_top', 'example-stats');
```

### Region System

```php
// Define regions in theme
<?= render_region('sidebar_top') ?>
<?= render_region('sidebar_bottom') ?>
<?= render_region('footer_widgets') ?>

// Render specific region
<?php foreach (get_region_widgets('sidebar_top') as $widget): ?>
    <div class="widget">
        <?= $widget->render() ?>
    </div>
<?php endforeach; ?>
```

### Standard Regions

- `sidebar_top`: Top of sidebar
- `sidebar_bottom`: Bottom of sidebar
- `main_before`: Before main content
- `main_after`: After main content
- `footer_left`: Left footer column
- `footer_right`: Right footer column
- `admin_dashboard`: Admin dashboard widgets
- `user_profile_top`: Top of user profile
- `user_profile_bottom`: Bottom of user profile

---

## Admin Interface

### Addon Management Pages

#### 1. Addon List Page (`/admin/addons`)

**Features**:
- Table of all addons with status (enabled/disabled)
- Search by name or author
- Filter by status
- Sort by name, version, downloads
- Actions: Enable, Disable, Configure, Delete
- View addon details (description, author, version)
- Dependency checking (show if addon depends on disabled addon)

**Data Displayed**:
- Addon name and icon
- Version number
- Author name
- Description
- Status badge (enabled/disabled)
- Dependency info
- Last updated date

#### 2. Addon Details Page (`/admin/addons/{name}`)

**Features**:
- Full addon information
- README content
- Version history
- Dependencies and reverse dependencies
- Changelog
- Link to addon documentation
- Link to addon author website
- Enable/Disable buttons
- Delete/Uninstall button

#### 3. Addon Configuration Page (`/admin/addons/{name}/config`)

**Features**:
- Dynamic form based on addon's config file
- Auto-save to addon config
- Validation based on config rules
- Reset to defaults option
- Save/Cancel buttons

#### 4. Addon Settings Panel in Admin Dashboard

Quick access to:
- Recently enabled/disabled addons
- Addons with updates available
- Addons with errors
- Quick enable/disable toggles

### Permissions

New permissions added:
- `addon.view`: View addon list and details
- `addon.manage`: Enable/disable addons
- `addon.configure`: Configure addon settings
- `addon.install`: Install new addons
- `addon.uninstall`: Remove addons

---

## API Reference

### Core Services

#### AddonManager

```php
class AddonManager
{
    // Discovery & Loading
    public function discover(): array;                    // Find all addons
    public function load(): void;                         // Load enabled addons
    public function getAddon(string $name): ?Addon;       // Get addon instance
    public function isEnabled(string $name): bool;
    public function getAll(): array;                      // Get all addons

    // Lifecycle
    public function enable(string $name): void;
    public function disable(string $name): void;
    public function install(string $name): void;
    public function uninstall(string $name): void;

    // Info
    public function getManifest(string $name): array;
    public function getVersion(string $name): string;
    public function getDependencies(string $name): array;
    public function hasDependency(string $name, string $dependency): bool;
}
```

#### HookManager

```php
class HookManager extends Events
{
    // Action hooks
    public function trigger(string $hook, ...$args): void;
    public function on(string $hook, callable $callback): void;
    public function off(string $hook, callable $callback): void;

    // Filter hooks
    public function filter(string $hook, $value, ...$args);
    public function addFilter(string $hook, callable $callback): void;

    // Discovery
    public function getHooks(): array;                     // List all hooks
    public function getHookInfo(string $hook): array;      // Get hook metadata
}
```

#### MenuManager

```php
class MenuManager
{
    public function register(string $location, string $id, array $item): void;
    public function unregister(string $location, string $id): void;
    public function get(string $location): array;
    public function getItem(string $location, string $id): ?array;
    public function canView(string $location, string $id): bool;
    public function sort(string $location): array;        // Sort by order
}
```

#### WidgetManager

```php
class WidgetManager
{
    public function register(string $id, string $class): void;
    public function getWidget(string $id): ?AbstractWidget;
    public function getWidgetsInRegion(string $region): array;
    public function renderRegion(string $region): string;
    public function addWidgetToRegion(string $region, string $widget): void;
    public function removeWidgetFromRegion(string $region, string $widget): void;
}
```

### Helper Functions

```php
// Addon utilities
addon_config(string $addon, string $key = null, $default = null);
addon_path(string $addon, string $path = ''): string;
addon_url(string $addon, string $path = ''): string;
addon_view(string $addon, string $view, array $data = []): string;
addon_enabled(string $addon): bool;

// Hook utilities
fire_hook(string $hook, ...$args);                        // Trigger hook
apply_filter(string $hook, $value, ...$args);             // Apply filter hook
add_hook(string $hook, callable $callback);                // Register listener

// Menu utilities
menu(string $location): array;
render_menu(string $location): string;

// Widget utilities
render_region(string $region): string;
add_region_widget(string $region, string $widget): void;
```

---

## Development Guide

### Creating Your First Module (After Phase 1)

Once core modules are in place, creating new modules is straightforward.

#### Step 1: Create Module Directory

```bash
mkdir -p addons/my-awesome-addon
cd addons/my-awesome-addon
```

#### Step 2: Create Module Manifest

```php
<?php
// module.php

return [
    'name'              => 'my-awesome-addon',
    'label'             => 'My Awesome Addon',
    'version'           => '1.0.0',
    'description'       => 'An awesome addon that does cool things',
    'author'            => 'Your Name',
    'type'              => 'addon',
    'required'          => false,

    'provider'          => 'MyAwesome\Addon\AwesomeProvider',
    'namespace'         => 'MyAwesome\Addon',

    'min_php'           => '8.4',
    'min_framework'     => '4.5',
    'dependencies'      => [
        'core-discussions',  // This addon requires core discussions
    ],

    'permissions'       => [
        'awesome.view' => 'View awesome feature',
        'awesome.manage' => 'Manage awesome feature',
    ],
];
```

#### Step 3: Create Module Provider

```php
<?php
// AwesomeProvider.php

namespace MyAwesome\Addon;

use App\Modules\Core\ModuleProvider;
use CodeIgniter\Events\Events;
use CodeIgniter\Router\RouteCollection;

class AwesomeProvider extends ModuleProvider
{
    public function manifest(): array
    {
        return require __DIR__ . '/module.php';
    }

    public function register(): void
    {
        // Register services in container
        $this->container->make(AwesomeService::class);
    }

    public function boot(): void
    {
        // Register hook listeners
        Events::on('post_created', [$this, 'onPostCreated']);
    }

    public function registerRoutes(RouteCollection $routes): void
    {
        $routes->group('awesome', ['namespace' => 'MyAwesome\Addon\Controllers'], function($routes) {
            $routes->get('/', 'AwesomeController::index', ['as' => 'index']);
            $routes->get('items', 'AwesomeController::items', ['as' => 'items']);
        });
    }

    public function registerMenuItems(): array
    {
        return [
            [
                'name'  => 'awesome-menu',
                'label' => 'My Awesome',
                'url'   => route('awesome-index'),
                'icon'  => 'fa-star',
                'order' => 50,
            ],
        ];
    }

    public function onPostCreated($post): void
    {
        log_message('info', 'New post: ' . $post->id);
    }
}
```

#### Step 4: Create Directory Structure

```bash
mkdir -p config
mkdir -p src/Controllers
mkdir -p src/Models
mkdir -p src/Services
mkdir -p src/Listeners
mkdir -p views
mkdir -p database/migrations
mkdir -p public/{css,js}
mkdir -p tests
```

#### Step 5: Create Routes File

```php
<?php
// config/Routes.php

use CodeIgniter\Router\RouteCollection;

return static function (RouteCollection $routes) {
    $routes->group('awesome', ['namespace' => 'MyAwesome\Addon\Controllers'], function($routes) {
        $routes->get('/', 'AwesomeController::index', ['as' => 'index']);
        $routes->get('items', 'AwesomeController::items', ['as' => 'items']);
    });
};
```

#### Step 6: Create a Controller

```php
<?php
// src/Controllers/AwesomeController.php

namespace MyAwesome\Addon\Controllers;

use CodeIgniter\Controller;

class AwesomeController extends Controller
{
    public function index()
    {
        return view('awesome::awesome/index');
    }

    public function items()
    {
        $items = model(ItemModel::class)->findAll();
        return view('awesome::awesome/items', ['items' => $items]);
    }
}
```

#### Step 7: Create a View

```php
<?php
// views/awesome/index.php

<?= $this->extend('master') ?>

<?= $this->section('main') ?>
    <h1>My Awesome Feature</h1>
    <p>This is my awesome addon.</p>
<?= $this->endSection() ?>
```

#### Step 8: Create README

```markdown
# My Awesome Addon

An awesome addon that does cool things.

## Installation

1. Extract addon to `addons/my-awesome-addon/`
2. Enable in admin panel at `/admin/modules`
3. Run migrations: `php spark migrate`

## Usage

Visit `/awesome` to see the addon in action.

## Hooks

### awesome:item_created
Fires when an awesome item is created.

**Parameters**:
- `$item` (AwesomeItem): The created item

**Example**:
```php
Events::on('awesome:item_created', function($item) {
    log_message('info', 'Awesome item created: ' . $item->id);
});
```

## Configuration

Configure via `AwesomeProvider::register()` in module provider.
```

---

### Using Hooks in Your Module

Hooks are the primary way modules communicate.

#### Listening to Events

```php
// In AwesomeProvider::boot()
Events::on('post_created', function($post) {
    // Do something when a post is created
    service(AwesomeService::class)->handleNewPost($post);
});

// Listen to filter hooks
Events::on('filter:post_display_data', function($data) {
    $data['awesome_rating'] = 5;
    return $data;
});
```

#### Triggering Your Own Hooks

```php
// In your controller or service
Events::trigger('awesome:item_created', $item);

// With filter hook
$data = Events::filter('filter:awesome_item_data', $itemData);
```

#### Available Hooks (Core)

See the Hook System section for full list. Core modules trigger events like:
- `discussions:thread_created`
- `discussions:thread_deleted`
- `posts:post_created`
- `posts:post_deleted`
- `moderation:action_taken`

---

### Testing Your Module

```bash
# Run module tests
php spark test addons/my-awesome-addon

# Enable/disable module via CLI
php spark module:enable my-awesome-addon
php spark module:disable my-awesome-addon

# Clear cache
php spark cache:clear
```

---

### Debugging

```php
// Check if module is enabled
if (service(ModuleManager::class)->isEnabled('my-awesome-addon')) {
    // Do something
}

// Get module instance
$module = service(ModuleManager::class)->getModule('my-awesome-addon');

// Check if dependencies are met
if ($module->validate()) {
    // Safe to use
}

---

## Implementation Roadmap

### Phase 1: Core Module Infrastructure (Weeks 1-3)

This phase sets up the foundation for all modules (core and future addons).

#### Week 1: Module System Foundation

- [ ] **Create ModuleManager** class
  - `discover()` - Find modules in `modules/` and `addons/`
  - `load()` - Load enabled modules
  - `getModule()`, `getAll()`, `getEnabled()`
  - Dependency resolution and ordering

- [ ] **Create ModuleProvider** abstract class
  - `register()` - Register services
  - `boot()` - Initialize after services registered
  - `manifest()` - Return module metadata
  - Other registration methods

- [ ] **Create Config/Modules.php**
  - List enabled/disabled modules
  - Module-level settings
  - Enable/disable toggles

- [ ] **Create Module Manifest Loader**
  - Parse `module.php` files
  - Validate required fields
  - Handle errors gracefully

- [ ] **Update Bootstrap Process**
  - Modify `public/index.php` to load ModuleManager
  - Load enabled modules before routing

#### Week 2: Module Directory Migration

- [ ] Create `modules/` directory structure
- [ ] Move core-discussions into `modules/core-discussions/`
  - Move controllers, models, entities, cells
  - Move routes to `config/Routes.php`
  - Move views to `views/`
  - Move migrations to `database/migrations/`
  - Create DiscussionsProvider.php
  - Create `module.php` manifest

- [ ] Move core-posts into `modules/core-posts/`
  - Same structure as core-discussions
  - Register dependency on core-discussions

- [ ] Move core-categories into `modules/core-categories/`
- [ ] Move core-moderation into `modules/core-moderation/`

- [ ] Update autoloading in composer.json
  - PSR-4 mapping for new module namespaces

- [ ] Update main Routes.php
  - Import routes from each module
  - Remove hardcoded route definitions

#### Week 3: Hook System Foundation

- [ ] **Create HookManager** class
  - Extends/wraps CodeIgniter Events
  - `trigger()` - Fire action hooks
  - `filter()` - Apply filter hooks
  - `on()` - Register listeners
  - `off()` - Remove listeners

- [ ] **Add Hook Points to Views/Controllers**
  - `navbar_before_items`
  - `navbar_after_items`
  - `sidebar_before_content`
  - `sidebar_after_content`
  - `thread_after_display`
  - `post_after_display`
  - etc. (refer to Hook System section)

- [ ] **Create Hook Documentation**
  - List all available hooks
  - Document parameters, returns
  - Show examples for each

- [ ] **Create Helper Functions**
  - `fire_hook()` - Trigger action hook
  - `apply_filter()` - Apply filter hook
  - `add_hook()` - Register listener

---

### Phase 2: Optional Core Modules (Weeks 4-5)

Move optional features into separate modules.

#### Week 4: Optional Features

- [ ] Extract `core-reactions` module
  - Move reaction-related code
  - Create ReactionsProvider.php
  - Register hooks for integration

- [ ] Extract `core-tagging` module
  - Move tagging code
  - Create TaggingProvider.php

- [ ] Extract `core-notifications` module
  - Move notification code
  - Create NotificationsProvider.php

#### Week 5: Standalone Modules

- [ ] Extract `core-user-profiles` module
- [ ] Extract `core-images` module
- [ ] Test enabling/disabling each optional module
- [ ] Verify no crashes when disabled

---

### Phase 3: Menu System (Week 6)

- [ ] **Create MenuManager** class
  - `register()` - Add menu item
  - `get()` - Get menu items for location
  - `remove()` - Remove menu item
  - `canView()` - Check permissions

- [ ] **Update Views** to use MenuManager
  - `_app_nav.php` → `<?= render_menu('navbar') ?>`
  - `account/_nav.php` → Use MenuManager
  - `moderation/_sidebar.php` → Use MenuManager

- [ ] **Register Menu Items** in Module Providers
  - Each module registers its menu items
  - `registerMenuItems()` in ModuleProvider

- [ ] **Helper Functions**
  - `menu()` - Get menu items
  - `render_menu()` - Render menu HTML

---

### Phase 4: Widget System (Week 7)

- [ ] **Create AbstractWidget** base class
- [ ] **Create WidgetManager** class
- [ ] **Create RegionManager** class
- [ ] **Define Standard Regions** in theme
- [ ] **Update Theme** to render regions
- [ ] **Admin UI** for widget management

---

### Phase 5: Admin Interface (Week 8)

- [ ] Module management page at `/admin/modules`
- [ ] Module details/configuration
- [ ] Enable/disable UI
- [ ] Dependency checking
- [ ] Module status indicators

---

### Phase 6: Addon System (Weeks 9-10)

Now that core is modular, build addon system on same foundation.

- [ ] Update ModuleManager to load from `addons/` directory
- [ ] Create addon examples:
  - Badge system
  - Reputation system
  - Statistics widget
- [ ] Create addon development guide
- [ ] Write tests for addon system

---

### Phase 7: Testing & Polish (Week 11)

- [ ] Unit tests for ModuleManager
- [ ] Unit tests for HookManager
- [ ] Unit tests for MenuManager
- [ ] Integration tests
- [ ] Performance testing
- [ ] Documentation cleanup

---

### Phase 8: Launch & Examples (Week 12)

- [ ] Merge to main branch
- [ ] Release documentation
- [ ] Create addon template repository
- [ ] Record video tutorials
- [ ] Community support

---

---

## Success Criteria

### Phase 1 (Core Refactoring)

**Functionality**:
- ✅ Core features extracted into 4 required modules + 5 optional modules
- ✅ All modules can be independently enabled/disabled
- ✅ No cross-module direct dependencies (all via hooks)
- ✅ Modules load in correct dependency order
- ✅ Routes properly registered from each module
- ✅ Migrations work per-module
- ✅ All original functionality preserved

**Performance**:
- ✅ Module loading < 50ms overhead
- ✅ Hook triggers < 5ms
- ✅ No degradation vs. monolithic version
- ✅ Optional modules can be disabled without loading

**Code Quality**:
- ✅ All core modules have > 80% test coverage
- ✅ Clear module boundaries enforced
- ✅ Zero circular dependencies
- ✅ All hooks documented with examples

**Developer Experience**:
- ✅ Clear module structure template
- ✅ Easy to move features into/out of modules
- ✅ ModuleProvider interface is simple and intuitive
- ✅ Helpful error messages for module issues

---

### Phase 2 (Addon System)

**Functionality**:
- ✅ Addons discovered and loaded like core modules
- ✅ Identical structure to core modules
- ✅ Can be enabled/disabled/installed/uninstalled
- ✅ Can register routes, controllers, models, migrations
- ✅ Can listen to all core hooks
- ✅ Can fire their own hooks
- ✅ Can register menus and widgets

**Admin Interface**:
- ✅ Module management page shows core + addon modules
- ✅ Dependency checking prevents broken states
- ✅ Status indicators clear
- ✅ Can enable/disable from UI
- ✅ Configuration interface for module settings

**Documentation**:
- ✅ Complete module development guide
- ✅ 3 working example addons (badges, reputation, stats)
- ✅ All hooks documented
- ✅ API reference complete
- ✅ Video tutorials showing module creation

---

## Technical Constraints

### Requirements
- PHP 8.4+
- CodeIgniter 4.5+
- Composer (for PSR-4 autoloading)
- Existing event system (extends it, not replaces)
- Existing permission/policy system

### Limitations
- Modules cannot override other modules' routes (same prefix forbidden)
- Modules should not modify each other's database schema
- Cannot disable required core modules
- Module load order is determined by dependencies, not arbitrary

### Security Considerations
- Module code runs with same privileges as core (trust source)
- All user input validation required in module code
- Permissions must be checked before feature access
- Module routes/controllers subject to normal security checks
- Database migrations run with full privileges

### Backward Compatibility

**During Phase 1 (Core Refactoring)**:
- Old `app/` paths still work during transition
- Gradual migration possible
- No breaking changes to external APIs

**After Phase 1**:
- Core functionality identical
- Admin URLs may change slightly (`/admin/addons` → `/admin/modules`)
- All existing routes, controllers, models work

**Phase 2 (Addon System)**:
- No changes to Phase 1 APIs
- Purely additive functionality
- Can coexist with any existing custom code

---

## Module Types Summary

| Module | Type | Can Disable | Depends On |
|--------|------|------------|-----------|
| core-discussions | Required | No | — |
| core-posts | Required | No | core-discussions |
| core-categories | Required | No | core-discussions |
| core-moderation | Required | No | core-discussions, core-posts |
| core-reactions | Optional | Yes | core-discussions, core-posts |
| core-tagging | Optional | Yes | core-discussions |
| core-notifications | Optional | Yes | core-discussions, core-posts |
| core-user-profiles | Optional | Yes | — |
| core-images | Optional | Yes | — |

---

## Key Principles Recap

1. **Modules are Equal Citizens**: Core modules use identical infrastructure to addons
2. **No Direct Dependencies**: All communication via hooks/events
3. **Graceful Degradation**: Disabling optional modules doesn't break app
4. **Separation of Concerns**: Each module owns its domain
5. **Testability First**: Each module independently testable
6. **Performance Conscious**: Lazy-load, cache appropriately
7. **Security-First**: No module can compromise core integrity

---

## Appendix: Glossary

- **Addon**: Third-party extension to forum functionality
- **Hook**: Point where addon code can execute or modify data
- **Service Provider**: Class that initializes addon
- **Menu Item**: Navigation item registered by addon
- **Widget**: Reusable UI component
- **Region**: Named area where widgets can be placed
- **Manifest**: addon.php file describing addon metadata
- **Filter Hook**: Modifies data before processing
- **Action Hook**: Executes code at specific point

---

## Appendix: Example Addon Structure

See `ADDON_EXAMPLES.md` for complete example addons including:
- Simple Hello World addon
- Badges addon with database
- Statistics dashboard widget addon

---

## Questions & Support

For questions during development, refer to:
1. This PRD document
2. Example addons in `addons/` directory
3. Hook documentation in each addon
4. API Reference section above
5. Code comments and inline documentation

---

**Document Status**: DRAFT - Ready for Development
**Last Updated**: November 2, 2025
**Next Review**: After Phase 2 Completion
