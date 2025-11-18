# Multi-Site Strategy: How to Leverage the Same Codebase

**Date:** November 2, 2025
**Status:** Strategic Design Complete
**Purpose:** Explain the cleanest architecture for Forum + Campaign Hub

---

## Executive Summary

The module-based architecture we're building solves your multi-site problem perfectly:

**Instead of**: Building a forum, then adding campaign features on top
**We do**: Build modular core that powers both sites independently

**Result**: One codebase, two distinct applications, shared infrastructure.

---

## The Cleanest Architecture (Already Documented)

See `MULTI_SITE_ARCHITECTURE.md` for complete details. Here's the summary:

### Three-Layer Architecture

```
┌─────────────────────────────────────────┐
│   Shared Application Code               │
│   (Config, Database, Authentication)    │
└─────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────┐
│   Module Manager & Bootstrapper         │
│   (Loads configured modules)            │
└─────────────────────────────────────────┘
           ↓
     ┌─────────┴─────────┐
     ↓                   ↓
┌──────────────┐   ┌──────────────┐
│ FORUM SITE   │   │CAMPAIGN SITE │
│ config/forum │   │config/campaign
│ modules: 8   │   │ modules: 13  │
│ theme:forum  │   │ theme:campaign
└──────────────┘   └──────────────┘
```

### Key Insight

Both sites use:
- Same core modules (auth, discussions, posts, moderation, etc.)
- Different configurations (which modules to load)
- Different themes (how to display)
- Different addons (optional features)

---

## What Changed in the Architecture

### Before (What You Were Building)
```
Forum Features (monolithic)
├── Discussions
├── Posts
├── Categories
├── Moderation
├── Reactions
├── Notifications
└── User Profiles

Then later:
Campaign Features (addon layer)
├── Campaigns
├── Wiki
├── Maps
├── Characters
└── Sessions
```

**Problem**: Campaign features are "bolted on" to forum. Hard to disable one or the other.

### After (New Architecture)
```
CORE MODULES (Shared by both sites)
├── core-auth
├── core-discussions     (includes posts/replies)
├── core-moderation
├── core-reactions
├── core-notifications
└── core-user-profiles

CAMPAIGN-ONLY MODULES (Loaded only on campaign site)
├── core-campaigns
├── core-wiki
├── core-maps
├── core-characters
└── core-sessions

THEMES (Different UI per site)
├── forum-theme
└── campaign-theme

ADDONS (Optional, per-site)
├── badge-system
├── reputation-system
├── campaign-notifications-addon
└── ...
```

**Benefit**: Both sites are "first-class citizens". Neither is secondary.

---

## Directory Structure

See `MULTI_SITE_ARCHITECTURE.md` for full directory tree. Key points:

```
modules/                    # Core modules (both sites)
├── core-auth/
├── core-discussions/       (includes posts/replies)
├── core-moderation/
├── core-reactions/
├── core-tagging/
├── core-notifications/
└── core-user-profiles/

modules-campaign/           # Campaign-only modules
├── core-campaigns/
├── core-wiki/
├── core-maps/
├── core-characters/
└── core-sessions/

themes/
├── forum/
└── campaign/

config/
├── forum.php              # Forum site config
├── campaign.php           # Campaign site config
└── shared.php

public/
├── index.php              # Forum site entry
└── index-campaign.php     # Campaign site entry
```

---

## How Module Loading Works

### Configuration Example

**Forum Site** (`config/forum.php`):
```php
<?php
return [
    'site_name' => 'My Forum',
    'theme' => 'forum',
    'modules' => [
        'core-auth',
        'core-discussions',
        'core-moderation',
        'core-reactions',
        'core-tagging',
        'core-notifications',
        'core-user-profiles',

        // Addons
        'badge-system',
        'reputation-system',
    ],
];
```

**Campaign Site** (`config/campaign.php`):
```php
<?php
return [
    'site_name' => 'Campaign Hub',
    'theme' => 'campaign',
    'modules' => [
        // Same core modules as forum
        'core-auth',
        'core-discussions',
        'core-moderation',
        'core-reactions',
        'core-tagging',
        'core-notifications',
        'core-user-profiles',

        // PLUS campaign-specific modules
        'core-campaigns',
        'core-wiki',
        'core-maps',
        'core-characters',
        'core-sessions',

        // Addons
        'campaign-notifications-addon',
    ],
];
```

### How ModuleManager Uses This

```php
// In app bootstrap
$config = config('Modules');  // Loaded based on entry point

$moduleManager = new ModuleManager($config->modules);
$moduleManager->loadAll();    // Load only configured modules

// Result: Forum site has 8 modules, Campaign has 13
```

---

## Entry Points

### Forum Site
```php
// public/index.php
define('SITE_TYPE', 'forum');
require_once ROOTPATH . 'sites/forum/bootstrap.php';

// This loads config/forum.php which specifies modules
```

### Campaign Site
```php
// public/index-campaign.php
define('SITE_TYPE', 'campaign');
require_once ROOTPATH . 'sites/campaign/bootstrap.php';

// This loads config/campaign.php which specifies modules
```

### Or Via Nginx
```nginx
server {
    server_name forum.example.com;
    root /var/www/forum-example/public;
    # Uses index.php
}

server {
    server_name campaigns.example.com;
    root /var/www/forum-example/public;
    # Routes to index-campaign.php
}
```

---
## Why This Works

### 1. Modules Are Site-Agnostic
Each module (core-discussions, core-reactions, etc.) doesn't know or care whether it's running on forum or campaign site. It just provides functionality via hooks/events.

### 2. Configuration Controls Loading
The site config determines which modules load. Nothing hardcoded.

### 3. Themes Handle Presentation
- Forum theme: Shows discussions, posts, categories, members
- Campaign theme: Shows campaigns, wiki, maps, characters, sessions

Same modules, different presentation.

### 4. Shared Database
Both sites use same database. Data isn't duplicated. A user in forum can also create/participate in campaigns.

### 5. Permissions Control Visibility
- Campaign forums: Permission system restricts access to campaign members
- User profiles: Can show different info based on context (forum vs campaign)

---

## What Stays the Same

### Users
- Single user account
- Can participate in forum AND campaigns
- Reputation/badges track across both

### Discussions & Posts
- Same discussion and post infrastructure
- Used for general forum AND campaign play-by-post
- Controlled via permissions

### Moderation
- Single moderation system
- Can moderate forum discussions OR campaign content
- Same moderators for both

### Notifications
- Same notification system
- Can be configured differently per site (via addons)
- Users get notifications for forum AND campaigns

---

## What's Different

### Campaign Hub Additions
```
+--- core-campaigns module
     ├─ Campaign model
     ├─ Campaign membership
     ├─ Campaign permissions
     └─ Campaign controllers

+--- core-wiki module
+--- core-maps module
+--- core-characters module
+--- core-sessions module
```

### UI (Theme)
```
Forum Theme                Campaign Theme
├── Homepage               ├── Homepage
│   ↓ Discussion list      │   ↓ Campaign list
├── Thread view            ├── Campaign details
├── User profiles          ├── Campaign wiki
│   ↓ Activity             ├── Maps
├── Categories             ├── Characters
└── Moderation             ├── Sessions (calendar)
                           └── Play-by-post forums
```

### Addons
```
Forum Addons              Campaign Addons
├── badge-system          ├── campaign-notifier
├── reputation-system     └── character-portrait
└── ...
```

---

## Database Considerations

### Single Database (Recommended)
```sql
-- Forum discussions
discussions (id, category_id, title, ...)

-- Campaign-specific
campaigns (id, name, ...)
campaign_membership (id, campaign_id, user_id, ...)
wiki_pages (id, campaign_id, ...)
maps (id, campaign_id, ...)
characters (id, campaign_id, ...)
sessions (id, campaign_id, ...)

-- Same infrastructure, used by both
posts (id, discussion_id, ...)  -- For replies/discussion
users (id, email, ...)
```

**Advantage**: No duplicate data. Users from forum naturally can be members of campaigns.

### Permissions Control Isolation
```php
// Campaign forums are just regular discussions with:
$discussion->type = 'campaign';
$discussion->campaign_id = 123;

// Permission system checks: Can user see this discussion?
if (discussion is campaign type) {
    if (user is campaign member) {
        show discussion
    }
}
```

---

## Addon Strategy

### Shared Addons
Work on both sites because they don't depend on site-specific features:

```php
// addons/badge-system
// Works for both forum AND campaign users
// Can badge them for: "First post", "Campaign creator", "Wiki contributor"
```

### Site-Specific Addons
Only relevant to one site:

```php
// addons/campaign-notifications-addon
'enabled_on_sites' => ['campaign'],  // Only load on campaign site

// addons/reputation-system
'enabled_on_sites' => ['forum'],     // Only load on forum site
```

### Addon Awareness
Addons can check which site they're running on:

```php
public function handleThreadCreated($thread) {
    $siteType = defined('SITE_TYPE') ? SITE_TYPE : 'forum';

    if ($siteType === 'campaign') {
        // Campaign-specific handling
    } else {
        // Forum-specific handling
    }
}
```

---

## Implementation Timeline

### Phase 1A: Module System (Weeks 1-3)
- Build ModuleManager
- Build ModuleBootstrapper
- Create Config/Modules.php
- Ready to load multiple sites

### Phase 1B: Refactor Forum (Weeks 4-6)
- Move forum code into core modules
- Create forum theme
- Test forum site works

### Phase 1C: Campaign Modules (Weeks 8-10)
- Build core-campaigns
- Build core-wiki
- Build core-maps
- Build core-characters
- Build core-sessions

### Phase 1D: Campaign Theme (Weeks 10-11)
- Build campaign-specific theme
- Design TTRPG aesthetic
- Test campaign site works

### Phase 2: Addon System (Weeks 12+)
- Build addon discovery
- Admin UI for addon management
- Create example addons

---

## Key Benefits

### ✅ Single Codebase
- One git repo
- One deployment process
- One version to track

### ✅ Independent Evolution
- Can update forum without affecting campaigns
- Can add campaign features without breaking forum
- Users see only what's relevant to their site

### ✅ Shared Infrastructure
- Users don't re-register
- Shared moderation system
- Shared notification system
- Shared account management

### ✅ Easy to Extend
- Add new site? Use different config + theme
- Add new feature? Create new module
- New site needs old feature? Create addon

### ✅ Team Friendly
- Different developers can own different modules
- Clear boundaries prevent conflicts
- Easy to test independently

---

## What's Different from Original Plan

### Original Plan
1. Build addon system for third-party features
2. Forum is monolithic core
3. Addons are secondary

### New Plan
1. Build module system for ALL functionality
2. Forum becomes a MODULE CONFIGURATION
3. Campaign is another MODULE CONFIGURATION
4. Addons are just additional modules following same pattern

**Result**: More powerful, more flexible, same effort.

---

## Potential Future Expansion

With this architecture, you could add:

### More Sites
- **Wiki Hub**: Just wiki + auth modules
- **Character Gallery**: Just characters + auth modules
- **Session Tracker**: Just sessions + auth modules

### Separate Deployments
- Deploy forum site to forum.example.com
- Deploy campaign site to campaigns.example.com
- Separate servers, shared database (if needed)
- Load balancing per site

### Microservices (Future)
- Extract modules into separate services
- Keep same hook/event communication
- Scale specific features independently

---

## Questions Answered

**Q: Isn't this overengineering?**
A: No. The module system is required for Phase 1 anyway. We're just extending it to support multiple sites. No extra work.

**Q: What if I want to add a third site later?**
A: Just create `config/wiki.php`, choose which modules to load, create a theme, and go. Same code, different config.

**Q: Won't users be confused switching between forum and campaigns?**
A: They're different URLs (forum.example.com vs campaigns.example.com), different themes, different navigation. Feels like different apps.

**Q: How do we handle private campaign forums?**
A: Same as public forums, but with permission check via policies. Campaign members can see private forums, others can't.

**Q: Can we share notifications between sites?**
A: Yes! Create a notification-aggregation addon that shows combined notifications from both sites.

---

## Next Steps

1. **Read** `MULTI_SITE_ARCHITECTURE.md` for complete technical specs
2. **Review** updated `ADDON_SYSTEM_PRD.md` (now includes multi-site section)
3. **Approve** this approach before Phase 1 begins
4. **Start** Phase 1A: Module system infrastructure

The architecture is solid. Let's build it!

---

**Document Status**: COMPLETE - Ready for Implementation
**Created**: November 2, 2025
