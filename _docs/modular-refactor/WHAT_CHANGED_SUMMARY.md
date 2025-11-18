# What Changed: From Single Forum to Multi-Site Architecture

**Summary of strategic decisions and why**

---

## Your Original Request

> "I want to create 2 different sites based on the same initial code base. The first would be a basic forum site, like it currently is being developed. The second site would then allow Game Masters to plan and organize their TTRPG campaigns..."

## The Original Approach (What You Were Doing)

You were building a forum application, and then planning to add campaign features as add-ons/mods on top.

```
Core Forum Code (Monolithic)
    ↓
Add campaign features
    ↓
Add wiki addon
    ↓
Add maps addon
    ↓
Result: Forum with campaign bolted on
```

**Problem**: Campaign site isn't a first-class citizen. It's secondary features.

---

## The New Approach (What We're Recommending)

Instead of treating the forum as the core and campaigns as add-ons, build the ENTIRE application as **independent modules** that can be mixed and matched.

```
Modular Components (All equal)
├── core-auth
├── core-discussions
├── core-posts
├── core-moderation
├── core-reactions
├── core-tagging
├── core-notifications
├── core-user-profiles
├── core-campaigns
├── core-wiki
├── core-maps
├── core-characters
└── core-sessions

Configuration A (Forum Site)
├── Load: core-auth, core-discussions, core-posts, core-moderation, core-reactions, core-tagging, core-notifications, core-user-profiles
├── Theme: forum-theme
└── Addons: badge-system, reputation-system

Configuration B (Campaign Site)
├── Load: ALL 13 modules
├── Theme: campaign-theme
└── Addons: campaign-notifier, character-portraits
```

**Result**: Two distinct applications, both first-class citizens, same codebase.

---

## Why This Is Better

### Old Approach Problems
❌ Forum is main app, campaigns are add-on
❌ Hard to disable campaign features if you just want a forum
❌ Campaign site feels like forum + features
❌ Architectural debt for second site

### New Approach Benefits
✅ Both sites are equal
✅ Choose exactly which features each site has
✅ Each site feels like it was built for that purpose
✅ Clean architecture from the start
✅ Easy to add third site later

---

## What's Different

| Aspect | Before | After |
|--------|--------|-------|
| **Architecture** | Monolithic forum | Modular core + configuration |
| **Forum Site** | Single app with all code | 8 configured modules + forum theme |
| **Campaign Site** | "Forum + new features" | 13 configured modules + campaign theme |
| **Code Organization** | app/ with everything mixed | modules/ with independent features |
| **Coupling** | Controllers/Models directly depend on each other | All modules via hooks/events |
| **New Site** | Would require more hacks | Just create new config + theme |
| **Addon System** | Would extend forum | Extends same pattern as core |
| **Testing** | Test entire app | Test modules independently |
| **Maintenance** | Changes can break both sites | Isolated changes |

---

## Key Insight

By making the forum modular first, we solve the campaign site problem **naturally**.

The campaign site isn't "forum + campaigns". It's just:
- "Which modules do I want?" → Campaigns + Wiki + Maps + Characters + Sessions + Forum modules
- "What theme?" → Campaign theme
- "What addons?" → Campaign-specific addons

The forum site is:
- "Which modules do I want?" → Just the discussion/community modules
- "What theme?" → Forum theme
- "What addons?" → Forum-specific addons

---

## Architecture Decision Matrix

```
                Single App      Multi-App       Modular
                (Original)      (Same Modules)  (New)
─────────────────────────────────────────────────────────
Code reuse      Forced          100%           100%
Separation      Low             Medium         High
Forum focus     ✓               ✗              ✓
Campaign focus  ✗               Partial        ✓
Extensibility   Low             Medium         High
Maintenance     Hard            Medium         Easy
Future sites    Very hard       Hard           Easy
```

We chose **Modular** because it maximizes every metric.

---

## What Stays the Same

These remain unchanged:
- PHP/CodeIgniter 4 as framework
- Database design philosophy
- User authentication approach
- React system
- Moderation philosophy
- Permission/policy system
- Notification approach

---

## What's New

1. **ModuleManager**: System to load/unload modules
2. **Module Configuration**: config/forum.php vs config/campaign.php
3. **Hook System**: Modules communicate via hooks instead of direct coupling
4. **Menu System**: Dynamic menu registration
5. **Widget System**: Pluggable UI components
6. **Multiple Entry Points**: index.php vs index-campaign.php
7. **Module Structure**: Each module self-contained with own routes/models/views

---

## Implementation Timeline Shift

### Before
- Week 1-4: Build forum
- Week 5-8: Add campaign features
- Week 9: Hope everything still works together
- Week 10+: Fix issues from tight coupling

### After
- Week 1-3: Build module system (infrastructure)
- Week 4-6: Extract forum into modules
- Week 8-10: Build campaign-only modules
- Week 10-11: Build campaign theme
- Week 12+: Add addon system
- Result: Both sites work independently, addon system is bonus

**Net change**: ~5 weeks to build it right instead of patching later.

---

## Code Example: The Difference

### Before (Forum-centric)
```php
// app/Controllers/ThreadController.php
public function create($categoryId) {
    // Direct coupling - assumes specific models exist
    $category = $this->categoryModel->find($categoryId);
    $thread = $this->threadModel->create(...);

    // Hard-coded notifications
    $this->notificationService->sendNewThreadNotification($thread);

    // Hard-coded reactions
    $this->reactionService->initializeFor($thread);
}
```

### After (Modular)
```php
// modules/core-discussions/src/Controllers/ThreadController.php
public function create($categoryId) {
    // No direct coupling - work with interfaces
    $thread = $this->threadModel->create(...);

    // Use hooks - other modules listen if they want
    Events::trigger('thread_created', $thread);

    // Result: core-notifications, core-reactions, addons all respond via hooks
}
```

When campaign site loads without core-notifications, thread creation still works—it just doesn't send notifications (because that module didn't load).

---

## Single Database Strategy

Both sites use the same database. Data isolation happens via:

1. **Data Type**: Column that marks data as 'general' or 'campaign'
2. **Permissions**: Check who can access what
3. **Context**: URL structure shows intent (/forums vs /campaigns)

```sql
-- Same table, different data types
discussions
├── id: 1, type: 'general', title: 'PHP Tips'         -- Forum site sees this
├── id: 2, type: 'campaign', campaign_id: 5, ...      -- Campaign site sees this
└── id: 3, type: 'campaign', campaign_id: 7, ...      -- Campaign site sees this
```

**Advantage**: Users can be part of both. One account, all features.

---

## Future-Proofing

This architecture enables:

### Short term (next year)
- Add reputation system addon to forum site
- Add character portrait addon to campaign site
- Add streaming integration addon for campaigns

### Medium term (2-3 years)
- Add third site (Character Gallery)
- Add fourth site (Session Scheduler)
- Each using same modules in different combinations

### Long term (3+ years)
- Extract modules into separate packages
- Deploy modules as separate services if needed
- Scale specific features independently

---

## Validation: Why This Works

✅ **WordPress does this**: Core plugins provide base functionality
✅ **Drupal does this**: Everything is modules
✅ **nopCommerce does this**: Features are selectable modules
✅ **Laravel ecosystem**: Multiple apps sharing packages

This is a proven pattern.

---

## What We Built (Documentation)

5 comprehensive documents explaining this:

1. **MULTI_SITE_STRATEGY.md** - Strategy & benefits (read first)
2. **ARCHITECTURE_VISUAL_GUIDE.md** - Diagrams & flows (visual learners)
3. **ARCHITECTURE_QUICK_REFERENCE.md** - Quick lookup (reference)
4. **MULTI_SITE_ARCHITECTURE.md** - Technical specs (implementation)
5. **ADDON_SYSTEM_PRD.md** - Complete PRD (specs)

Plus:
- **PHASE_1_IMPLEMENTATION_GUIDE.md** - Week-by-week tasks
- **ARCHITECTURE_DOCUMENTATION_INDEX.md** - Navigation guide

---

## Ready to Implement?

### Before You Start

1. ✅ Read MULTI_SITE_STRATEGY.md (understand why)
2. ✅ Review ARCHITECTURE_VISUAL_GUIDE.md (understand how)
3. ✅ Review ARCHITECTURE_QUICK_REFERENCE.md (quick facts)

### When You're Ready

1. Study MULTI_SITE_ARCHITECTURE.md (technical details)
2. Follow PHASE_1_IMPLEMENTATION_GUIDE.md (tasks)
3. Reference ADDON_SYSTEM_PRD.md (specs)

---

## Summary

**You asked**: How to build two sites from one codebase?

**We answered**: Build one modular framework that can be configured as either site (or both, or others).

**Why it's better**:
- Cleaner architecture
- Easier maintenance
- More flexible
- Future-proof
- Proven pattern

**Timeline**: ~11 weeks to have both sites production-ready with addon system.

**Next**: Start Phase 1A (Module System) - Week 1

---

**This is the right architecture. Let's build it! 🚀**
