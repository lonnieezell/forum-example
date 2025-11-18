# Multi-Site Architecture Strategy: Forum + Campaign Manager

**Version:** 1.0
**Date:** November 2, 2025
**Status:** Strategic Design
**Purpose:** Architecture for deploying the same codebase as two different applications

---

## Table of Contents

1. [Vision](#vision)
2. [Use Cases](#use-cases)
3. [Cleanest Architecture](#cleanest-architecture)
4. [Project Structure](#project-structure)
5. [Module Strategy](#module-strategy)
6. [Theme System](#theme-system)
7. [Addon Strategy](#addon-strategy)
8. [Shared Components](#shared-components)
9. [Site-Specific Customization](#site-specific-customization)
10. [Deployment Strategy](#deployment-strategy)
11. [Implementation Approach](#implementation-approach)

---

## Vision

A **single, unified codebase** that can be deployed as two distinct applications:

1. **Forum Site** (current): General-purpose community discussion forum
   - Public forums for any topic
   - General user discussions
   - Simple user profiles
   - Moderation and reputation

2. **Campaign Hub** (new): TTRPG campaign management platform
   - Campaign wiki for world-building
   - Maps and location management
   - Campaign-specific forums (play-by-post)
   - Character sheets and management
   - Campaign scheduling and sessions
   - Rich narrative/story tracking

**Same codebase. Different configuration. Different theme. Different addons.**

---

## Use Cases

### Site 1: Public Forum
```
URL: forum.example.com
Type: Public community forum
Features Enabled:
  ✅ Discussions (public categories)
  ✅ Posts/Replies
  ✅ User profiles
  ✅ Reactions (likes, hearts)
  ✅ Tagging
  ✅ Moderation
  ✅ Notifications

Features Disabled:
  ❌ Campaigns module
  ❌ Campaign permissions
  ❌ Wiki module
  ❌ Maps module

Theme: forum-theme (simple, clean, discussion-focused)
```

### Site 2: Campaign Manager
```
URL: campaigns.example.com
Type: Private campaign management + play-by-post
Features Enabled:
  ✅ Discussions (campaign-specific categories)
  ✅ Posts/Replies (play-by-post)
  ✅ User profiles
  ✅ Reactions
  ✅ Tagging
  ✅ Moderation
  ✅ Notifications
  ✅ Campaigns module (NEW)
  ✅ Campaign permissions
  ✅ Wiki module (NEW)
  ✅ Maps module (NEW)
  ✅ Characters module (NEW)
  ✅ Sessions module (NEW)

Theme: campaign-theme (fantasy-themed, campaign-focused)
```

---

## Cleanest Architecture

### The Foundation: Modular Core

The cleanest architecture is to build the core forum as **independent, composable modules** from day one:

```
┌─────────────────────────────────────────────────────────────┐
│         APPLICATION BOOTSTRAP & SHARED SERVICES              │
│  (Config, Database, Authentication, Service Container)      │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│              MODULE MANAGER & LOADER                         │
│  (Discovery, Loading, Dependency Resolution, Bootstrapping) │
└─────────────────────────────────────────────────────────────┘
                            ↓
        ┌───────────────────┴───────────────────┐
        ↓                                       ↓
┌───────────────────────┐          ┌───────────────────────┐
│   CORE MODULES        │          │  SITE-SPECIFIC MODULES│
│   (Shared base)       │          │  (Forum or Campaign)  │
├───────────────────────┤          ├───────────────────────┤
│ • Auth & Users        │          │ • Campaigns           │
│ • Discussions         │          │ • Wiki                │
│ • Posts               │          │ • Maps                │
│ • Categories          │          │ • Characters          │
│ • Moderation          │          │ • Sessions            │
│ • Notifications       │          │                       │
│ • Reactions           │          │ (Loaded only on      │
│ • Tagging             │          │  campaign site)       │
└───────────────────────┘          └───────────────────────┘
        ↓                                   ↓
        └───────────────────┬───────────────────┘
                            ↓
        ┌───────────────────────────────────────┐
        │   HOOK/EVENT SYSTEM                   │
        │ (Inter-module communication)          │
        │ (All modules communicate via hooks)   │
        └───────────────────────────────────────┘
                            ↓
        ┌───────────────────────────────────────┐
        │   THEME SYSTEM                        │
        │ • forum-theme                         │
        │ • campaign-theme                      │
        │ (Same modules, different UI)          │
        └───────────────────────────────────────┘
                            ↓
        ┌───────────────────────────────────────┐
        │   ADDON MODULES (Third-party)         │
        │ • Badges                              │
        │ • Reputation System                   │
        │ • Campaign notifications addon        │
        │ • Forum moderation addons             │
        └───────────────────────────────────────┘
```

**Key Insight**: By building the core as modules, the Campaign Manager isn't "extending" the forum—it's just a **different selection and configuration of the same modules**, plus additional campaign-specific modules.

---

## Project Structure

### Directory Layout

```
forum-example/                          # Project root
├── app/                                # Shared application code
│   ├── Config/
│   │   ├── App.php
│   │   ├── Database.php
│   │   ├── Modules.php                 # ← Module enablement
│   │   └── Routes.php                  # ← Route loading
│   ├── Bootstrap/
│   │   └── ModuleManager.php           # ← Core module system
│   ├── Concerns/                       # Shared traits
│   └── Managers/
│       ├── ModuleManager.php           # ← Module loading/lifecycle
│       ├── HookManager.php             # ← Hook/event system
│       ├── MenuManager.php
│       └── WidgetManager.php
│
├── modules/                            # Core modules (shipped with app)
│   ├── core-auth/                      # User auth & management
│   │   ├── config/
│   │   ├── src/
│   │   ├── views/
│   │   ├── database/migrations/
│   │   └── ModuleServiceProvider.php
│   ├── core-discussions/               # Categories, threads, posts/replies
│   ├── core-moderation/                # Moderation tools
│   ├── core-reactions/                 # Like/heart reactions
│   ├── core-tagging/                   # Tag system
│   ├── core-notifications/             # Notification engine
│   └── core-user-profiles/             # User profile management
│
├── modules-campaign/                   # Campaign-specific core modules
│   ├── core-campaigns/                 # Campaign management
│   │   ├── config/
│   │   ├── src/
│   │   ├── views/
│   │   ├── database/migrations/
│   │   └── ModuleServiceProvider.php
│   ├── core-wiki/                      # Campaign wiki
│   ├── core-maps/                      # Map management
│   ├── core-characters/                # Character management
│   └── core-sessions/                  # Campaign sessions
│
├── themes/
│   ├── forum/                          # Forum-specific theme
│   │   ├── css/
│   │   ├── js/
│   │   └── views/
│   └── campaign/                       # Campaign-specific theme
│       ├── css/
│       ├── js/
│       └── views/
│
├── addons/                             # Third-party modules
│   ├── badge-system/
│   ├── reputation-system/
│   └── campaign-notifier-addon/
│
├── config/
│   ├── forum.php                       # ← Forum site configuration
│   ├── campaign.php                    # ← Campaign site configuration
│   └── shared.php                      # ← Shared configuration
│
├── public/
│   ├── index.php                       # ← Loads appropriate config
│   └── index-campaign.php              # ← Alternative entry point
│
└── sites/                              # Site-specific customization
    ├── forum/
    │   ├── config/
    │   ├── routes.php
    │   └── bootstrap.php
    └── campaign/
        ├── config/
        ├── routes.php
        └── bootstrap.php
```

---

## Module Strategy

### Three Categories of Modules

#### 1. **Shared Core Modules** (`modules/core-*`)
These modules are loaded for **both** forum and campaign sites because they're fundamental to both:

- `core-auth`: User authentication (both sites need users)
- `core-discussions`: Categories, threads, posts/replies (both use these)
- `core-moderation`: Moderation tools (both need this)
- `core-notifications`: Notifications (both use this)
- `core-reactions`: Like/love reactions (both use this)
- `core-tagging`: Tagging system (both use this)
- `core-user-profiles`: User profiles (both use this)

#### 2. **Campaign-Specific Core Modules** (`modules-campaign/core-*`)
These modules are loaded **only** on the campaign site:

- `core-campaigns`: Campaign management (create, manage, permissions)
- `core-wiki`: Campaign wiki/reference
- `core-maps`: Map management and viewing
- `core-characters`: Character management and sheets
- `core-sessions`: Campaign session scheduling and tracking

#### 3. **Addon Modules** (`addons/`)
These can be loaded on either site:

- `badge-system`: User badges and achievements
- `reputation-system`: Reputation points
- `campaign-notifications-addon`: Campaign-specific notifications
- `stream-integration-addon`: Integration with streaming platforms
- `character-portrait-addon`: Better character portrait management

### Configuration: Which Modules Load Where?

**Forum Site** (`config/forum.php`):
```php
<?php

return [
    'site_name' => 'My Forum',
    'modules' => [
        // Core shared modules
        'core-auth',
        'core-discussions',
        'core-moderation',
        'core-notifications',
        'core-reactions',
        'core-tagging',
        'core-user-profiles',

        // Add-ons for forum
        'badge-system',
        'reputation-system',
    ],
    'theme' => 'forum',
];
```

**Campaign Site** (`config/campaign.php`):
```php
<?php

return [
    'site_name' => 'Campaign Hub',
    'modules' => [
        // Core shared modules (same as forum)
        'core-auth',
        'core-discussions',
        'core-moderation',
        'core-notifications',
        'core-reactions',
        'core-tagging',
        'core-user-profiles',

        // Campaign-specific core modules
        'core-campaigns',
        'core-wiki',
        'core-maps',
        'core-characters',
        'core-sessions',

        // Add-ons for campaign
        'badge-system',
        'campaign-notifications-addon',
    ],
    'theme' => 'campaign',
];
```

---

## Theme System

### Two Themes for Different UIs

Both sites use the **same underlying modules and data**, but present them differently through **themes**.

#### Forum Theme (`themes/forum/`)
- Clean, discussion-focused layout
- Sidebar: Categories, recent topics
- Main: Thread list, discussion threads
- User profiles: Simple, activity-focused
- Navigation: Home, Categories, Members, Help

#### Campaign Theme (`themes/campaign/`)
- Fantasy/RPG aesthetic
- Sidebar: Campaign selection, quick access to wiki/maps/characters
- Main: Campaign play-by-post forums with rich narrative context
- User profiles: Character-centric with character portraits
- Navigation: My Campaigns, Wiki, Maps, Characters, Sessions

**Important**: The theme doesn't define functionality—only presentation. Both themes render the same underlying modules.

#### Shared Theme Components
Some theme files are shared (base layouts):
```
themes/
├── shared/
│   ├── master.php           # Base layout template
│   ├── navbar.php           # Navigation structure
│   └── components/          # Shared components
├── forum/
│   ├── layouts/
│   │   └── discussion.php   # Forum-specific layout
│   ├── views/
│   │   └── discussions/
│   └── css/
└── campaign/
    ├── layouts/
    │   └── campaign.php     # Campaign-specific layout
    ├── views/
    │   ├── campaigns/
    │   └── wiki/
    └── css/
```

---

## Addon Strategy

### Shared Addons
Some addons work for both sites:

```php
// addons/badge-system/config/Config.php
// Works for both forum AND campaign users
'badge_conditions' => [
    'first_post' => 'Posted for the first time',
    'campaign_creator' => 'Created a campaign',
    'wiki_contributor' => 'Contributed to campaign wiki',
];
```

### Site-Specific Addons

```php
// addons/reputation-system/config/Config.php
// Can have site-specific settings
'enabled_on_sites' => ['forum'],  // Only on forum site

// addons/campaign-notifications-addon/config/Config.php
'enabled_on_sites' => ['campaign'],  // Only on campaign site
```

### Addon Hooks Must Be Site-Aware

```php
// In addon hook listener
Events::on('thread_created', function($thread) {
    $site = config('App')->site_type;  // 'forum' or 'campaign'

    if ($site === 'campaign') {
        // Campaign-specific handling
    } else {
        // Forum-specific handling
    }
});
```

---

## Shared Components

### What Lives in `app/` (Shared)

```
app/
├── Config/
│   ├── App.php                  # Base app config
│   ├── Database.php             # Database config (shared)
│   ├── Auth.php                 # Auth config (shared)
│   ├── Modules.php              # Module loading config
│   └── Routes.php               # Routes bootstrapper
│
├── Bootstrap/
│   └── ModuleBootstrapper.php   # Loads modules based on config
│
├── Managers/
│   ├── ModuleManager.php        # Core module system
│   ├── HookManager.php          # Event/hook system
│   ├── MenuManager.php
│   └── WidgetManager.php
│
├── Concerns/
│   ├── HasTimestamps.php        # Shared trait
│   ├── HasAuthorship.php        # Shared trait
│   └── RendersContent.php       # Shared trait
│
└── Support/
    ├── helpers.php              # Helper functions
    └── ServiceProvider.php      # Base for module providers
```

### What Gets Site-Specific

```
sites/
├── forum/
│   ├── bootstrap.php            # Forum-specific bootstrap
│   ├── config/                  # Override configs
│   └── routes.php               # Additional routes
│
└── campaign/
    ├── bootstrap.php            # Campaign-specific bootstrap
    ├── config/                  # Override configs
    └── routes.php               # Additional routes
```

---

## Site-Specific Customization

### Entry Points

**Forum Site** (`public/index.php`):
```php
<?php

// Load forum-specific config
define('SITE_TYPE', 'forum');
require_once ROOTPATH . 'sites/forum/bootstrap.php';

// Load forum configuration
$config = require_once ROOTPATH . 'config/forum.php';

// Continue with normal CodeIgniter bootstrap...
require_once ROOTPATH . 'app/Config/Boot.php';
```

**Campaign Site** (`public/index-campaign.php`):
```php
<?php

// Load campaign-specific config
define('SITE_TYPE', 'campaign');
require_once ROOTPATH . 'sites/campaign/bootstrap.php';

// Load campaign configuration
$config = require_once ROOTPATH . 'config/campaign.php';

// Continue with normal CodeIgniter bootstrap...
require_once ROOTPATH . 'app/Config/Boot.php';
```

### Nginx Configuration (If Separate Domains)

```nginx
# forum.example.com
server {
    server_name forum.example.com;
    root /var/www/forum-example/public;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}

# campaigns.example.com
server {
    server_name campaigns.example.com;
    root /var/www/forum-example/public;
    location / {
        # Route to campaign entry point
        rewrite ^/(.*)$ /index-campaign.php?$1 last;
    }
}
```

### Or Use Subdirectories

```
public/
├── forum/
│   ├── index.php                # forum.example.com/forum
│   └── .htaccess
└── campaign/
    ├── index.php                # forum.example.com/campaign
    └── .htaccess
```

---

## Deployment Strategy

### Single Codebase, Dual Deployment

```
┌─────────────────────────────────────┐
│      Git Repository                 │
│   forum-example (master branch)     │
└─────────────────────────────────────┘
        ↓                    ↓
┌──────────────────┐   ┌──────────────────┐
│ Production #1    │   │ Production #2    │
│ forum.example    │   │ campaigns.example│
│ .com             │   │ .com             │
├──────────────────┤   ├──────────────────┤
│ /var/www/        │   │ /var/www/        │
│ forum-example    │   │ forum-example    │
│ (same code)      │   │ (same code)      │
│                  │   │                  │
│ Entry: index.php │   │ Entry: index-    │
│                  │   │ campaign.php     │
│ Config:          │   │ Config:          │
│ config/forum.php │   │ config/campaign. │
│                  │   │ php              │
│ Theme: forum     │   │ Theme: campaign  │
│ Modules: 8       │   │ Modules: 13      │
└──────────────────┘   └──────────────────┘
```

### Docker Setup (Optional)

```dockerfile
# Dockerfile (shared by both)
FROM php:8.4-fpm

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Environment determines which site to run
ENV SITE_TYPE=forum  # or 'campaign'

CMD ["php-fpm"]
```

Then launch with different environment:
```bash
# Forum site
docker run -e SITE_TYPE=forum ...

# Campaign site
docker run -e SITE_TYPE=campaign ...
```

---

## Implementation Approach

### Phase 1A: Module System Foundation (Weeks 1-3)
Build the core module infrastructure that both sites will use:
1. ModuleManager and ModuleBootstrapper
2. HookManager (event system)
3. Config/Modules.php with enablement logic
4. Test with shared core modules

### Phase 1B: Refactor Forum into Core Modules (Weeks 4-6)
Extract current forum code into:
- core-auth
- core-discussions (includes categories, threads, posts)
- core-moderation
- core-reactions
- core-tagging
- core-notifications
- core-user-profiles

### Phase 1C: Create Forum Theme (Week 6-7)
Build forum-specific theme that works with modules.

### Phase 2: Campaign-Specific Modules (Weeks 8-10)
Build campaign-only modules:
- core-campaigns
- core-wiki
- core-maps
- core-characters
- core-sessions

### Phase 3: Campaign Theme (Week 10-11)
Build campaign-specific theme with fantasy aesthetic.

### Phase 4: Addon System (Weeks 12+)
Build addon discovery, enable/disable, admin UI, etc.

---

## Benefits of This Approach

### ✅ DRY (Don't Repeat Yourself)
- Single codebase for both sites
- No code duplication
- Changes to core benefit both

### ✅ Independent Evolution
- Forum and Campaign sites can evolve independently
- Different themes don't require code changes
- New campaign modules don't affect forum

### ✅ Easy Maintenance
- Bug fixes in core-discussions benefit both sites
- Can upgrade shared modules atomically
- Clear versioning per module

### ✅ Seamless Third-Party Integration
- Addons work on both sites if configured to
- Addon developers only learn one pattern
- No special "hooks" per site

### ✅ Testability
- Test each module independently
- Test Forum site configuration
- Test Campaign site configuration
- Test addon integration

### ✅ Scalability
- Could easily add third site using subset of modules
- Could extract modules into separate packages
- Could transition to microservices later if needed

---

## Potential Future Expansion

With this architecture, you could add:

### Additional Sites
- **Wiki Hub**: Just core-wiki + core-auth + campaign wiki modules
- **Character Gallery**: core-characters + core-auth + gallery-addon
- **Session Tracker**: core-sessions + core-auth + calendar-addon

### Separate Deployments Per Site
- Each site as separate EC2 instance/container
- Shared database, separate caches
- Load balancing across instances

### Microservices Migration Path
When scaling requires it, extract modules into microservices:
```
Services:
- auth-service
- discussion-service
- campaign-service
(All communicate via same hook/event system)
```

---

## Migration Strategy

### Minimal Disruption
1. Build module system alongside existing code
2. Gradually move code into modules
3. Have both old and new code run simultaneously
4. Remove old code when modules are stable
5. Switch production to module-based code

### Rollback Plan
- Keep git history of functional forum
- Can revert to tag if issues arise
- Stage modules in separate branch first

---

## Questions to Clarify

Before implementing, confirm:

1. **Database**: Single database for both sites, or separate?
   - **Recommendation**: Single database, use `site_type` column to partition data if needed

2. **User Accounts**: Shared user accounts or separate?
   - **Recommendation**: Shared. Users can participate in forum AND campaigns

3. **Private Forums**: How are campaign forums private?
   - **Recommendation**: Permission system via policies (per-forum access control)

4. **Campaign Permissions**: Do campaign members see private wiki/maps?
   - **Recommendation**: Addon system adds campaign-specific permissions

5. **Themes**: Can users switch themes?
   - **Recommendation**: Per-site fixed theme, users can choose dark/light variant

6. **Shared Addons**: Can badge-system work on campaign site?
   - **Recommendation**: Yes, with conditions tailored per site

---

## Summary

**The Cleanest Architecture**:

1. **Build core as modules** (doesn't depend on being "forum" or "campaign")
2. **Create configuration per site** (which modules to load)
3. **Build site-agnostic themes** (themes just render modules)
4. **Use hooks for customization** (addons customize behavior)
5. **Deploy same code** (different entry points, different configs)

This way, the Campaign Hub isn't a "new feature" bolted onto the forum—it's a **different application using the same building blocks**.

---

**Next Step**: Should I create a revised PRD that incorporates this multi-site architecture into Phase 1 planning?
