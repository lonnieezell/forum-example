# Quick Reference: Multi-Site Module Architecture

**TL;DR version of the entire architecture strategy**

---

## The Concept (30 seconds)

Build ONE codebase that can be deployed as TWO sites:
- **Forum Site** (forum.example.com): Public discussions
- **Campaign Site** (campaigns.example.com): TTRPG campaign management

Same core modules, different configurations, different themes, different addons.

---

## How It Works (2 minutes)

```
Same Codebase
    ↓
Different Config (config/forum.php vs config/campaign.php)
    ↓
Different Modules Loaded (8 vs 13)
    ↓
Different Theme (forum-theme vs campaign-theme)
    ↓
Same Database (shared users, data context determines visibility)
    ↓
Two distinct applications with shared infrastructure
```

---

## Key Files Created

| File | Purpose |
|------|---------|
| `ADDON_SYSTEM_PRD.md` | Complete PRD with multi-site architecture section |
| `MULTI_SITE_ARCHITECTURE.md` | Detailed technical specs (structure, deployment, migration) |
| `MULTI_SITE_STRATEGY.md` | Strategy explanation and benefits |
| `ARCHITECTURE_VISUAL_GUIDE.md` | Visual diagrams and flows |
| `ARCHITECTURE_QUICK_REFERENCE.md` | This file - quick lookup |

---

## Directory Structure

```
modules/                    → Shared modules (both sites)
├── core-auth/
├── core-discussions/       (includes categories, threads, posts)
├── core-moderation/
├── core-reactions/
├── core-tagging/
├── core-notifications/
└── core-user-profiles/

modules-campaign/           → Campaign-only modules
├── core-campaigns/
├── core-wiki/
├── core-maps/
├── core-characters/
└── core-sessions/

themes/
├── forum/                  → Forum UI
└── campaign/               → Campaign UI

config/
├── forum.php               → Forum: 8 modules + addons
├── campaign.php            → Campaign: 13 modules + addons
└── shared.php

public/
├── index.php               → forum.example.com
└── index-campaign.php      → campaigns.example.com
```

---

## Configuration Pattern

**Forum Site** (`config/forum.php`):
```php
['modules' => [
    'core-auth',
    'core-discussions',
    'core-moderation',
    'core-reactions',
    'core-tagging',
    'core-notifications',
    'core-user-profiles',
    'badge-system',
    'reputation-system',
], 'theme' => 'forum']
```

**Campaign Site** (`config/campaign.php`):
```php
['modules' => [
    // All 7 shared modules
    'core-auth',
    'core-discussions',
    'core-moderation',
    'core-reactions',
    'core-tagging',
    'core-notifications',
    'core-user-profiles',
    // PLUS 5 campaign modules
    'core-campaigns',
    'core-wiki',
    'core-maps',
    'core-characters',
    'core-sessions',
    'campaign-notifications-addon',
], 'theme' => 'campaign']
```

---

## Module Categories

### Shared Modules (Both Sites)
- `core-auth`: User authentication
- `core-discussions`: Categories, threads, and posts/replies
- `core-moderation`: Moderation system
- `core-reactions`: Likes, hearts, reactions
- `core-tagging`: Tag system
- `core-notifications`: Notification engine
- `core-user-profiles`: User profiles

### Campaign-Only Modules
- `core-campaigns`: Campaign management
- `core-wiki`: Campaign wiki
- `core-maps`: Map management
- `core-characters`: Character sheets
- `core-sessions`: Campaign sessions/scheduling

```

### Addon Modules (Optional, Per-Site)
- `badge-system`: Badges (works on both)
- `reputation-system`: Reputation (forum site)
- `campaign-notifications-addon`: Campaign notifications (campaign site)
- `character-portrait-addon`: Character portraits (campaign site)

---

## Shared vs Site-Specific

| Component | Shared | Forum Only | Campaign Only |
|-----------|--------|-----------|---------------|
| Database | ✓ | | |
| Users | ✓ | | |
| Auth | ✓ | | |
| Discussions | ✓ | | |
| Posts | ✓ | | |
| Moderation | ✓ | | |
| Reactions | ✓ | | |
| Tagging | ✓ | | |
| Notifications | ✓ | | |
| User Profiles | ✓ | | |
| Campaigns | | | ✓ |
| Wiki | | | ✓ |
| Maps | | | ✓ |
| Characters | | | ✓ |
| Sessions | | | ✓ |
| Reputation | | ✓ | |
| Campaign Notifier Addon | | | ✓ |

---

## Entry Points

| URL | File | Config | Modules |
|-----|------|--------|---------|
| forum.example.com | public/index.php | config/forum.php | 8 core + 2 addons |
| campaigns.example.com | public/index-campaign.php | config/campaign.php | 8 core + 5 campaign + 2 addons |

---

## How Data Works

### Single Database
Both sites share the same database. Data isolation happens via:

1. **Data Type/Context**: `type='general'` for forum, `type='campaign'` for campaigns
2. **Permissions**: Policy checks who can access what
3. **Module Configuration**: Campaign modules only on campaign site

### User Accounts
Single user account works on BOTH sites:
- User registers once
- Can participate in forum discussions
- Can create/join campaigns
- Can access both sites with same login

### Private Data
Campaign data is private via permissions:
```php
// Only campaign members can see campaign threads
if ($discussion->type === 'campaign') {
    if (!$user->isInCampaign($discussion->campaign_id)) {
        return forbidden();
    }
}
```

---

## Development Workflow

### Phase 1A: Module System (Weeks 1-3)
- Build ModuleManager
- Create ModuleBootstrapper
- Set up Config/Modules.php
- Enable loading multiple sites

### Phase 1B: Forum Refactor (Weeks 4-6)
- Move code into modules/core-*
- Create forum theme
- Verify forum site works

### Phase 1C: Campaign Modules (Weeks 8-10)
- Build core-campaigns
- Build core-wiki
- Build core-maps
- Build core-characters
- Build core-sessions

### Phase 1D: Campaign Theme (Weeks 10-11)
- Create campaign-specific UI
- Design fantasy aesthetic
- Verify campaign site works

### Phase 2: Addon System (Weeks 12+)
- Build addon discovery
- Build addon admin UI
- Create example addons

---

## Module System Infrastructure

What needs to be built in Phase 1A:

```
app/Config/Modules.php
├─ Modules to load
├─ Modules to disable
└─ Module ordering

app/Managers/ModuleManager.php
├─ discover()           → Find modules
├─ load(config)         → Load specified modules
├─ enable(name)         → Enable module
├─ disable(name)        → Disable module
├─ getLoaded()          → Get loaded modules
└─ resolve(deps)        → Verify dependencies

app/Bootstrap/ModuleBootstrapper.php
├─ Register module routes
├─ Register module services
├─ Register module hooks
└─ Register module migrations

Each Module:
├─ ModuleServiceProvider.php
│  ├─ register()
│  ├─ boot()
│  ├─ getRoutes()
│  └─ getHooks()
├─ config/Routes.php
├─ src/Controllers/
├─ src/Models/
└─ views/
```

---

## Testing Strategy

```
Test Forum Configuration
├─ Load config/forum.php
├─ Verify 8 modules load
├─ Check campaign features NOT available
└─ Test forum functionality

Test Campaign Configuration
├─ Load config/campaign.php
├─ Verify 13 modules load
├─ Check campaign features ARE available
├─ Check forum features still work
└─ Test campaign functionality

Test Data Isolation
├─ Create public forum thread
├─ Create private campaign thread
├─ Verify correct access via permissions
└─ Verify correct visibility in UI
```

---

## Performance Targets

| Metric | Target | Actual |
|--------|--------|--------|
| Module load time per module | <10ms | TBD |
| Forum site startup | <100ms | TBD |
| Campaign site startup | <150ms | TBD |
| Per-request overhead | <1ms | TBD |
| Memory per module | <1MB | TBD |
| Route lookup time | <1ms | TBD |

---

## Key Decisions Made

✅ **Single codebase**: One repo, one deployment
✅ **Module-first**: All features as modules (both core and addons)
✅ **Configuration-driven**: Config file determines site identity
✅ **Shared database**: Single DB, data isolation via permissions
✅ **Theme-based UI**: Same modules, different presentation
✅ **Hook-based communication**: Modules talk via events
✅ **Phase 1 first**: Build module system before campaign features

---

## Benefits Summary

| Benefit | Impact |
|---------|--------|
| Single codebase | Lower maintenance, easier updates |
| Module-first | Better separation of concerns |
| Configuration-driven | Easy to add new sites |
| Shared infrastructure | Users work across sites |
| Permission-based isolation | Private campaigns secure |
| Future scalability | Can extract modules to services later |
| Team-friendly | Clear boundaries for developers |
| Extensible | Addon system built on same patterns |

---

## Common Questions

**Q: Won't this be overengineering?**
A: No. Module system is Phase 1 requirement anyway. Multi-site support is natural extension.

**Q: What if someone forgets to configure a module?**
A: ModuleManager validates dependencies and throws clear error.

**Q: Can we deploy sites separately?**
A: Yes. Same code, different servers if needed. Shared database.

**Q: How do we handle addon conflicts?**
A: Addons declare dependencies. ModuleManager prevents loading conflicting addons.

**Q: What about database migrations per site?**
A: Migrations can check which modules are loaded. Only run if module is active.

**Q: Can existing users migrate?**
A: Yes. User account exists on both sites after Phase 1B complete.

---

## Next Steps

1. **Read** MULTI_SITE_STRATEGY.md (10 min)
2. **Review** ARCHITECTURE_VISUAL_GUIDE.md (5 min)
3. **Study** MULTI_SITE_ARCHITECTURE.md (20 min)
4. **Approve** approach
5. **Start** Phase 1A (Module system)

---

## Files to Review (In Order)

1. **MULTI_SITE_STRATEGY.md** ← Start here (strategy explanation)
2. **ARCHITECTURE_VISUAL_GUIDE.md** ← Then here (diagrams and flows)
3. **MULTI_SITE_ARCHITECTURE.md** ← Then here (technical specs)
4. **ADDON_SYSTEM_PRD.md** ← Finally here (complete PRD)

---

**Status**: Architecture Complete - Ready for Phase 1 Implementation
**Date**: November 2, 2025
**Last Updated**: November 2, 2025
