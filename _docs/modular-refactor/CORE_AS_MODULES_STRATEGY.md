# Core-as-Modules Architecture: Strategy & Changes

**Date**: November 2, 2025
**Decision**: Refactor core application into independent modules before building addon system
**Timeline**: 3 weeks (Phase 1) + 2 weeks (Phase 2)

---

## What Changed from Original Plan?

### Original Plan (from PRD v1.0)
- ❌ Keep core monolithic
- ❌ Build addon system on top of monolithic core
- ✅ Addons use hooks to extend

### New Plan (Core-as-Modules)
- ✅ Refactor core into independent modules first (Phase 1)
- ✅ Build addon system that addons share same infrastructure as core (Phase 2)
- ✅ All modules (core + addon) communicate via hooks

---

## Why This is Better

### 1. **Cleaner Architecture**
Before: Monolithic core + bolted-on addons
After: Core modules + addon modules all equal citizens

### 2. **Better Testing**
Before: Hard to test in isolation
After: Each module tested independently

### 3. **Better Documentation**
Before: Core modules are examples for addons
After: Core IS modules; core docs = addon docs

### 4. **Flexibility**
Before: Can't disable core features
After: Can disable optional features (reactions, notifications, etc.)

### 5. **Scalability**
Before: Hard to split teams by feature
After: Each team owns a module

### 6. **Easier to Understand**
Before: New developers learn monolithic + addon system
After: New developers learn module system (applies to everything)

---

## What's Different in PRD v2.0?

### Terminology
- "Addon" → "Module" (more generic term)
- "Addon System" → "Module System"
- All modules treated identically (core vs addon)

### Core Module List (NEW)

**Required** (cannot disable):
- `core-discussions` - Forum structure (categories, threads)
- `core-posts` - Replies and comments
- `core-categories` - Category management
- `core-moderation` - Moderation queue

**Optional** (can disable):
- `core-reactions` - Likes, hearts, emojis
- `core-tagging` - Thread tags
- `core-notifications` - User notifications
- `core-user-profiles` - User profile pages
- `core-images` - Image upload/storage

### Architecture Components (NEW)

**ModuleManager**: Central service managing all modules
**ModuleProvider**: Base class each module extends
**Module Manifest** (`module.php`): Metadata file for each module
**HookManager**: Centralized hook/event system

### File Structure (NEW)

```
Before (Monolithic):
app/
├── Controllers/
├── Models/
├── Views/
└── Routes.php

After (Modular):
modules/                          ← Core modules
├── core-discussions/
├── core-posts/
├── core-categories/
└── core-moderation/

addons/                           ← Third-party modules (Phase 2)
├── badge-system/
└── reputation/
```

---

## Phase 1 vs Phase 2

### Phase 1: Core Refactoring (3 weeks)

**Goal**: Establish modular foundation

**What Happens**:
1. Build ModuleManager infrastructure
2. Move core features into `modules/` directory
3. Each module has own routes, controllers, models, views
4. Modules communicate via hooks (not direct calls)
5. All functionality identical to before
6. Can enable/disable optional modules

**What Users See**: Nothing different (backward compatible)

**What Developers See**:
- New `modules/` directory
- New module.php files
- New ModuleProvider classes
- Hook points in views/controllers

### Phase 2: Addon System (2 weeks, after Phase 1)

**Goal**: Enable third-party addon development

**What Happens**:
1. ModuleManager extended to load `addons/` directory
2. Addons use identical module structure
3. Admin UI for managing modules
4. Menu system for addon menu items
5. Widget system for addon widgets

**What Users See**:
- Admin panel with Module Management
- Can enable/disable modules
- Can install addons

---

## Key Principles

### 1. No Direct Coupling
❌ WRONG:
```php
$threadModel = model('ThreadModel');
$posts = model('PostModel')->where('thread_id', $id)->get();
```

✅ RIGHT:
```php
// Posts module listens to thread events
Events::on('thread_loaded', function($thread) {
    $posts = service(PostService::class)->getForThread($thread->id);
});
```

### 2. Module Autonomy
Each module owns:
- Its routes
- Its controllers
- Its models
- Its views
- Its migrations
- Its database tables

### 3. Explicit Dependencies
Modules declare what they depend on:
```php
'dependencies' => [
    'core-discussions',  // This module requires core-discussions
]
```

### 4. Event-Driven Communication
Modules communicate via hooks:
- Action hooks: Execute code at points
- Filter hooks: Modify data before processing

---

## Implementation Order

### Phase 1, Week 1
- Build ModuleManager
- Build ModuleProvider
- Build Config/Modules
- Build bootstrap changes
- Build HookManager + helpers

### Phase 1, Week 2
- Migrate core-discussions to module
- Migrate core-posts to module
- Migrate core-categories to module
- Migrate core-moderation to module

### Phase 1, Week 3
- Add hook points throughout app
- Ensure modules use hooks for communication
- Test all combinations
- Fix any issues

### Phase 2 (Future)
- Extend ModuleManager for addons
- Build menu system
- Build widget system
- Build admin UI
- Create example addons

---

## What Stays the Same

✅ All existing functionality works
✅ All routes and URLs same
✅ All database tables same
✅ All user-facing features identical
✅ Performance identical
✅ Security identical

## What Changes

🔄 Core features in `modules/` instead of `app/`
🔄 New `module.php` files for metadata
🔄 New ModuleProvider pattern
🔄 Explicit hook points for extensibility
🔄 Config/Modules.php for enabling/disabling

---

## Why This Matters for Addons

With core-as-modules, addon developers see:

**Before**:
- Core = monolithic, hard to understand
- Addons = different structure/pattern
- Have to learn two different systems

**After**:
- Core = example modules
- Addons = just more modules
- Only one system to learn

This makes the addon system much more approachable and understandable.

---

## Risk Management

### What Could Go Wrong?

1. **Breaking something during refactor**
   - Mitigation: Do one module at a time, test each step
   - Rollback: Can disable module or revert commits

2. **Performance degradation**
   - Mitigation: Profile during implementation
   - Expected: Zero impact (same code, just organized differently)

3. **Dependencies not resolved correctly**
   - Mitigation: Test enabling/disabling modules
   - Example: Test posts module with discussions disabled

### Rollback Strategy

If issues arise:
1. Git branch for all changes
2. Can revert to before refactoring
3. Can disable specific module causing issues
4. Can fix and re-merge

---

## Success Definition

Phase 1 is successful when:

- ✅ Core features extracted into 9 modules
- ✅ All features work identically to before
- ✅ No direct module-to-module coupling
- ✅ Modules communicate only via hooks
- ✅ Optional modules can be disabled
- ✅ Performance identical to before
- ✅ Code quality improved (better organized)
- ✅ Tests pass (80%+ coverage)

---

## Documents to Reference

1. **ADDON_SYSTEM_PRD.md** - Complete requirements and architecture
2. **PHASE_1_IMPLEMENTATION_GUIDE.md** - Day-by-day implementation steps
3. **This document** - Strategy and rationale

---

## Next Steps

1. Review this strategy document
2. Review ADDON_SYSTEM_PRD.md v2.0
3. Review PHASE_1_IMPLEMENTATION_GUIDE.md
4. Start with Phase 1, Week 1, Day 1
5. Track progress in TODO lists

Ready to begin? Start with Phase 1!
