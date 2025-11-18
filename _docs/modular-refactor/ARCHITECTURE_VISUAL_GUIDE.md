# Architecture Overview: Visual Guide

**Quick reference for the multi-site, module-based architecture**

---

## The Big Picture

```
┌─────────────────────────────────────────────────────────────────┐
│                    YOUR CODEBASE                                │
│              forum-example (Single Git Repo)                    │
└─────────────────────────────────────────────────────────────────┘
              ↓                              ↓
    ┌─────────────────────────┐   ┌─────────────────────────┐
    │  forum.example.com      │   │ campaigns.example.com   │
    │  (Public Forum Site)    │   │ (Campaign Hub Site)     │
    ├─────────────────────────┤   ├─────────────────────────┤
    │ Entry: index.php        │   │ Entry: index-campaign.  │
    │ Config: config/forum.   │   │ Config: config/campaign │
    │ Theme: forum-theme      │   │ Theme: campaign-theme   │
    │ Modules: 8 core         │   │ Modules: 8 core + 5     │
    │          2 addons       │   │          2 addons       │
    └─────────────────────────┘   └─────────────────────────┘
              ↓                              ↓
    ┌───────────────────────────────────────────────────────┐
    │         SHARED INFRASTRUCTURE                         │
    ├───────────────────────────────────────────────────────┤
    │ • Single Database                                     │
    │ • User Accounts                                       │
    │ • Authentication                                      │
    │ • Service Container                                   │
    │ • Module Manager                                      │
    │ • Hook/Event System                                   │
    └───────────────────────────────────────────────────────┘
```

---

## Module Loading Per Site

### Forum Site
```
config/forum.php specifies:

✅ core-auth              (Users & authentication)
✅ core-discussions       (Categories, threads, posts)
✅ core-moderation        (Moderation system)
✅ core-reactions         (Likes, hearts, etc)
✅ core-tagging           (Tag system)
✅ core-notifications     (Notifications)
✅ core-user-profiles     (User profiles)
❌ core-campaigns         (NOT loaded)
❌ core-wiki              (NOT loaded)
❌ core-maps              (NOT loaded)
❌ core-characters        (NOT loaded)
❌ core-sessions          (NOT loaded)

Total: 7 modules
```

### Campaign Site
```
config/campaign.php specifies:

✅ core-auth              (Users & authentication)
✅ core-discussions       (Categories, threads, posts)
✅ core-moderation        (Moderation system)
✅ core-reactions         (Likes, hearts, etc)
✅ core-tagging           (Tag system)
✅ core-notifications     (Notifications)
✅ core-user-profiles     (User profiles)
✅ core-campaigns         (Campaign management)
✅ core-wiki              (Campaign wiki)
✅ core-maps              (Map management)
✅ core-characters        (Character sheets)
✅ core-sessions          (Campaign sessions)

Total: 13 modules
```

---

## How It Works: Request Flow

### User visits forum.example.com

```
HTTP Request
    ↓
public/index.php
    ↓
Sets SITE_TYPE = 'forum'
    ↓
Loads config/forum.php
    ↓
ModuleManager::loadAll()
    ├─ Loads core-auth
    ├─ Loads core-discussions
    ├─ Loads core-moderation
    ├─ Loads core-reactions
    ├─ Loads core-tagging
    ├─ Loads core-notifications
    ├─ Loads core-user-profiles
    └─ (campaign modules NOT loaded)
    ↓
Routes initialize from loaded modules
    ↓
Theme: forum-theme
    ├─ navbar.php
    ├─ discussion-list.php
    └─ css/forum.scss
    ↓
Renders public forum
```

### User visits campaigns.example.com

```
HTTP Request
    ↓
public/index-campaign.php
    ↓
Sets SITE_TYPE = 'campaign'
    ↓
Loads config/campaign.php
    ↓
ModuleManager::loadAll()
    ├─ Loads core-auth
    ├─ Loads core-discussions
    ├─ Loads core-moderation
    ├─ Loads core-reactions
    ├─ Loads core-tagging
    ├─ Loads core-notifications
    ├─ Loads core-user-profiles
    ├─ Loads core-campaigns          ← ALSO loaded
    ├─ Loads core-wiki               ← ALSO loaded
    ├─ Loads core-maps               ← ALSO loaded
    ├─ Loads core-characters         ← ALSO loaded
    └─ Loads core-sessions           ← ALSO loaded
    ↓
Routes initialize from loaded modules
    ↓
Theme: campaign-theme
    ├─ navbar.php
    ├─ campaign-list.php
    ├─ wiki.php
    ├─ map-viewer.php
    └─ css/campaign.scss
    ↓
Renders campaign hub with rich features
```

---

## File Structure Overview

```
forum-example/
│
├── app/
│   ├── Config/
│   │   ├── Modules.php          ← Module system config
│   │   └── Routes.php           ← Routes bootstrapper
│   ├── Managers/
│   │   └── ModuleManager.php    ← Loads modules
│   └── Bootstrap/
│       └── ModuleBootstrapper.php
│
├── modules/                      ← Shared modules (all sites)
│   ├── core-auth/
│   ├── core-discussions/         (includes posts/replies)
│   ├── core-moderation/
│   ├── core-reactions/
│   ├── core-tagging/
│   ├── core-notifications/
│   └── core-user-profiles/
│
├── modules-campaign/             ← Campaign-only modules
│   ├── core-campaigns/
│   ├── core-wiki/
│   ├── core-maps/
│   ├── core-characters/
│   └── core-sessions/
│
├── themes/
│   ├── forum/                    ← Forum UI
│   │   ├── views/
│   │   ├── css/
│   │   └── js/
│   └── campaign/                 ← Campaign UI
│       ├── views/
│       ├── css/
│       └── js/
│
├── config/
│   ├── forum.php                 ← Forum config
│   └── campaign.php              ← Campaign config
│
└── public/
    ├── index.php                 ← Forum entry
    └── index-campaign.php        ← Campaign entry
```

---

## Data Flow: Same Modules, Different Context

### Example: User Creates Thread

```
FORUM SITE: Create Thread
    ↓
User → /discussions/new
    ↓
PostController from core-discussions (loaded)
    ↓
Creates discussion with type='general'
    ↓
Triggers hooks: 'thread_created'
    ↓
Hooks fire in loaded modules:
  • core-notifications: sends notification
  • core-reactions: initializes reaction counter
  • badge-system addon: checks for achievements
    ↓
Result: Public thread in forum


CAMPAIGN SITE: Create Campaign Thread
    ↓
User → /campaigns/123/discussions/new
    ↓
CampaignController from core-campaigns (loaded)
    ↓
Creates discussion with type='campaign', campaign_id=123
    ↓
Triggers hooks: 'thread_created'
    ↓
Hooks fire in loaded modules:
  • core-notifications: sends campaign-specific notification
  • core-campaigns: updates campaign activity
  • core-reactions: initializes reaction counter
  • campaign-notifications addon: sends notification to campaign members
    ↓
Result: Private thread in campaign (only members see)
```

**Key Point**: Same module, same code, different behavior based on data context (type, campaign_id) and loaded modules.

---

## Permissions: How Private Forums Work

### Public Forum (Forum Site)
```
Thread visibility:
├─ Created with type='general'
└─ Everyone can view (public)

Policy check:
├─ Anonymous users → CAN view
├─ Logged in users → CAN view
└─ All users → Can see thread
```

### Private Campaign Forum (Campaign Site)
```
Thread visibility:
├─ Created with type='campaign' + campaign_id=123
└─ Only campaign members can view

Policy check:
├─ Not in campaign → CANNOT view
├─ In campaign → CAN view
└─ Only authorized users → Can see thread
```

---

## Adding a New Feature

### If feature is for BOTH sites (e.g., better reactions)

1. Create module: `modules/core-reactions-advanced/`
2. Add to BOTH configs:
   ```php
   // config/forum.php
   'modules' => [..., 'core-reactions-advanced']

   // config/campaign.php
   'modules' => [..., 'core-reactions-advanced']
   ```
3. Both sites automatically have new feature

### If feature is for ONE site (e.g., campaign-specific streamer integration)

1. Create addon: `addons/streamer-integration-addon/`
2. Add to ONLY that config:
   ```php
   // config/campaign.php only
   'modules' => [..., 'streamer-integration-addon']
   ```
3. Only campaign site has feature

---

## Development Workflow

### Building a New Module

```
1. Create directory: modules/core-newfeature/

2. Create ModuleServiceProvider.php
   ├─ register(): Register services
   ├─ boot(): Initialize
   └─ getRoutes(): Define routes

3. Create config/Routes.php
   └─ Module-specific routes

4. Create src/
   ├─ Controllers/
   ├─ Models/
   └─ Widgets/

5. Test on both sites:
   ├─ php spark serve (forum)
   └─ curl forum.local/newfeature

   ├─ php spark serve (campaign)
   └─ curl campaign.local/newfeature
```

### Testing Module Combinations

```
Test Suite 1: Forum Config
├─ Load 8 core modules
├─ Verify routes work
└─ Check no campaign features appear

Test Suite 2: Campaign Config
├─ Load 13 modules
├─ Verify routes work
├─ Check campaign features appear
└─ Check forum features still work

Test Suite 3: Custom Config
├─ Load different subset
├─ Verify dependencies met
└─ Check no missing functionality
```

---

## Performance Impact

### Module Loading
- Each module loaded: ~5-10ms
- Forum site: 8 modules = ~50-80ms startup
- Campaign site: 13 modules = ~80-130ms startup
- **Impact**: Minimal (~80ms one-time on app startup)

### Per-Request
- ModuleManager: Cached, < 1ms
- Route lookup: Cached, < 1ms
- Hook system: Standard Events, < 1ms
- **Impact**: Zero measurable difference

### Memory
- Each module: ~500KB average
- Forum site: 8 modules = ~4MB
- Campaign site: 13 modules = ~6.5MB
- **Impact**: Negligible for web app

---

## FAQ

**Q: What if campaign site visitors accidentally go to forum domain?**
A: Different domain, different site. They see forum, not campaigns. Intentional separation.

**Q: Can we share user data between sites?**
A: Yes! Single database, same users table. Users exist across both sites.

**Q: What about admin features for each site?**
A: Admin interface is module. Can be shared or site-specific.

**Q: How do we handle moderation across sites?**
A: Moderation module works for both. Moderators can moderate forum OR campaigns.

**Q: Can we migrate from forum-only to multi-site later?**
A: Yes, but module system must be in place first (Phase 1A).

---

## Related Documents

- **ADDON_SYSTEM_PRD.md**: Complete PRD with all specifications
- **MULTI_SITE_ARCHITECTURE.md**: Detailed technical architecture
- **MULTI_SITE_STRATEGY.md**: Strategy and benefits explanation
- **PHASE_1_IMPLEMENTATION_GUIDE.md**: Step-by-step implementation tasks

---

**Quick Start**: Read MULTI_SITE_STRATEGY.md for full context, then reference this document as you develop!
