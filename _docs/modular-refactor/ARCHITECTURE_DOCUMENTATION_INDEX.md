# Architecture Documentation Index

**Complete guide to the Forum + Campaign Hub multi-site architecture**

---

## 📋 Document Overview

This directory contains comprehensive documentation for deploying a single codebase as two distinct applications:

1. **Public Forum Site** (forum.example.com)
2. **Campaign Hub Site** (campaigns.example.com)

---

## 📚 Documents (Read in This Order)

### 1. **MULTI_SITE_STRATEGY.md** ⭐ START HERE
**Purpose**: Explain the strategy and why it works
**Read Time**: 15 minutes
**Contains**:
- Executive summary of the approach
- Benefits and trade-offs
- How module loading works
- Addon strategy
- What changed from original plan
- Future expansion possibilities

**Action**: Understand the vision before diving into technical details

---

### 2. **ARCHITECTURE_VISUAL_GUIDE.md**
**Purpose**: Visual diagrams and flowcharts
**Read Time**: 10 minutes
**Contains**:
- Big picture diagram
- Module loading per site
- Request flow diagrams
- File structure overview
- Data flow examples
- Performance impact analysis
- FAQ section

**Action**: See how it all fits together visually

---

### 3. **ARCHITECTURE_QUICK_REFERENCE.md**
**Purpose**: TL;DR quick lookup guide
**Read Time**: 5 minutes
**Contains**:
- 30-second concept explanation
- Key files and structure
- Configuration pattern
- Module categories table
- Development workflow phases
- Testing strategy
- Common questions

**Action**: Use as a reference while developing

---

### 4. **MULTI_SITE_ARCHITECTURE.md**
**Purpose**: Complete technical specification
**Read Time**: 30 minutes
**Contains**:
- Detailed directory structure
- Namespace conventions
- Module categorization (shared vs campaign-specific)
- Configuration file examples
- Entry point specifications
- Deployment strategies
- Database considerations
- Addon strategy details
- Migration strategy
- Clarifying questions

**Action**: Understand all technical details before implementation

---

### 5. **ADDON_SYSTEM_PRD.md**
**Purpose**: Complete PRD with all specifications
**Read Time**: 60 minutes
**Contains**:
- Full feature requirements (FR-01 through FR-05)
- Core module specifications (8 shared modules)
- Module system infrastructure details
- Module lifecycle and bootstrapping
- Hook system (15+ hooks documented)
- Menu system
- Widget system
- Admin interface specifications
- Complete API reference
- Development guide with examples
- 10-week implementation roadmap
- Success criteria

**Action**: Reference during implementation of each component

---

### 6. **PHASE_1_IMPLEMENTATION_GUIDE.md**
**Purpose**: Step-by-step implementation tasks
**Read Time**: 20 minutes (reference as needed)
**Contains**:
- Day-by-day tasks for Phases 1A, 1B, 1C, 1D
- Code examples for key components
- Testing checklist
- Rollback procedures
- Deployment checklist

**Action**: Follow during development of Phase 1

---

## 🎯 Quick Navigation

### If you want to understand...

| Question | Document | Section |
|----------|----------|---------|
| "What's the overall strategy?" | MULTI_SITE_STRATEGY.md | Overview |
| "How does module loading work?" | ARCHITECTURE_VISUAL_GUIDE.md | Module Loading Per Site |
| "What files do I need to create?" | MULTI_SITE_ARCHITECTURE.md | Directory Structure |
| "How do I configure a site?" | ARCHITECTURE_QUICK_REFERENCE.md | Configuration Pattern |
| "What modules need to be built?" | ADDON_SYSTEM_PRD.md | Core Module Specifications |
| "What are the hooks?" | ADDON_SYSTEM_PRD.md | Hook System |
| "What's my first task?" | PHASE_1_IMPLEMENTATION_GUIDE.md | Phase 1A Week 1 |
| "How do permissions work?" | ARCHITECTURE_VISUAL_GUIDE.md | Permissions section |
| "Can I deploy separately?" | MULTI_SITE_ARCHITECTURE.md | Deployment Strategy |
| "What about the database?" | MULTI_SITE_ARCHITECTURE.md | Database Considerations |

---

## 🏗️ Architecture at a Glance

```
┌─────────────────────────────────────┐
│     Single Git Repository           │
│    forum-example (same codebase)    │
└─────────────────────────────────────┘
    ↙                            ↘
┌──────────────────┐     ┌──────────────────┐
│  Forum Site      │     │  Campaign Site   │
│ index.php        │     │ index-campaign.php
│ config/forum.php │     │ config/campaign.php
│ 8 modules        │     │ 13 modules       │
│ forum-theme      │     │ campaign-theme   │
└──────────────────┘     └──────────────────┘
    ↓                            ↓
┌──────────────────────────────────────┐
│  Shared Infrastructure              │
│  • Database                          │
│  • Users                             │
│  • ModuleManager                     │
│  • HookManager                       │
│  • MenuManager                       │
│  • WidgetManager                     │
└──────────────────────────────────────┘
```

---

## 📦 Core Modules

### Shared Modules (Both Sites)
- `core-auth` - User authentication
- `core-discussions` - Categories and threads
- `core-posts` - Posts and replies
- `core-moderation` - Moderation system
- `core-reactions` - Likes, hearts, etc
- `core-tagging` - Tag system
- `core-notifications` - Notifications
- `core-user-profiles` - User profiles

### Campaign-Only Modules
- `core-campaigns` - Campaign management
- `core-wiki` - Campaign wiki
- `core-maps` - Map management
- `core-characters` - Character sheets
- `core-sessions` - Campaign sessions

---

## 🔄 Development Phases

### Phase 1A: Module System Foundation (Weeks 1-3)
Build infrastructure for loading modules:
- ModuleManager class
- ModuleBootstrapper class
- Config/Modules.php
- Support for multiple site configurations

### Phase 1B: Refactor Forum (Weeks 4-6)
Extract forum into modules:
- Move code into modules/core-*
- Create forum theme
- Test forum site works independently

### Phase 1C: Campaign Modules (Weeks 8-10)
Build campaign-specific modules:
- core-campaigns
- core-wiki
- core-maps
- core-characters
- core-sessions

### Phase 1D: Campaign Theme (Weeks 10-11)
- Create campaign UI theme
- Design fantasy aesthetic
- Test campaign site works

### Phase 2: Addon System (Weeks 12+)
- Build addon discovery system
- Create admin UI for addons
- Create example addons

---

## 🚀 Getting Started

### For Strategic Understanding
1. Read **MULTI_SITE_STRATEGY.md** (understand the why)
2. Review **ARCHITECTURE_VISUAL_GUIDE.md** (see the how)
3. Reference **ARCHITECTURE_QUICK_REFERENCE.md** (quick facts)

### For Implementation
1. Study **MULTI_SITE_ARCHITECTURE.md** (technical details)
2. Review **ADDON_SYSTEM_PRD.md** (specifications)
3. Follow **PHASE_1_IMPLEMENTATION_GUIDE.md** (step-by-step)

### For Reference During Development
- Keep **ARCHITECTURE_QUICK_REFERENCE.md** open
- Check **ARCHITECTURE_VISUAL_GUIDE.md** for diagrams
- Reference **ADDON_SYSTEM_PRD.md** for API specs

---

## 💾 File Locations

All documentation files are in the repository root:

```
forum-example/
├── ADDON_SYSTEM_PRD.md              (Complete PRD)
├── ARCHITECTURE_QUICK_REFERENCE.md  (Quick lookup)
├── ARCHITECTURE_VISUAL_GUIDE.md     (Diagrams & flows)
├── CORE_AS_MODULES_STRATEGY.md      (Original strategy doc)
├── DOCUMENTATION_INDEX.md           (Legacy index)
├── DOCUMENTATION_README.md          (Legacy readme)
├── IMPLEMENTATION_SUMMARY.md        (Legacy summary)
├── MULTI_SITE_ARCHITECTURE.md       (Technical specs)
├── MULTI_SITE_STRATEGY.md           (Strategy explanation)
├── PHASE_1_IMPLEMENTATION_GUIDE.md  (Task breakdown)
└── README.md                        (Project README)
```

---

## ❓ FAQ

**Q: Which document should I read first?**
A: Start with MULTI_SITE_STRATEGY.md. It explains the entire vision.

**Q: Where's the implementation plan?**
A: PHASE_1_IMPLEMENTATION_GUIDE.md has day-by-day tasks for weeks 1-3.

**Q: What modules do I need to build?**
A: See ADDON_SYSTEM_PRD.md → Core Module Specifications. Also ARCHITECTURE_QUICK_REFERENCE.md → Module Categories.

**Q: How do I test this?**
A: See ARCHITECTURE_QUICK_REFERENCE.md → Testing Strategy. Also PHASE_1_IMPLEMENTATION_GUIDE.md → Testing Checklist.

**Q: Can I deploy these separately?**
A: Yes. See MULTI_SITE_ARCHITECTURE.md → Deployment Strategy.

**Q: What's the database setup?**
A: Single database, both sites. See MULTI_SITE_ARCHITECTURE.md → Database Considerations.

**Q: How do permissions work?**
A: See ARCHITECTURE_VISUAL_GUIDE.md → Permissions section.

**Q: What about addons?**
A: See ADDON_SYSTEM_PRD.md → Hook System and MULTI_SITE_STRATEGY.md → Addon Strategy.

---

## 🔍 Key Concepts

### Module
A self-contained feature (auth, discussions, campaigns, etc.) with own controllers, models, views, routes, and migrations.

### Module Configuration
File (config/forum.php or config/campaign.php) that specifies which modules to load for that site.

### ModuleManager
Service that discovers and loads modules based on configuration.

### Hook
Extension point where addons/other modules can inject code or data.

### Theme
Different UI presentation for the same underlying modules.

### Site Type
Forum (index.php) or Campaign (index-campaign.php). Determines which config loads.

### Shared Infrastructure
Database, users, authentication, service container shared by both sites.

---

## 📊 Success Metrics

From ADDON_SYSTEM_PRD.md Success Criteria:

- ✅ Core refactored into 6+ independent modules
- ✅ Zero direct coupling between modules (all via hooks)
- ✅ All module hooks documented with examples
- ✅ Can disable optional modules without crashing
- ✅ Module loading < 50ms overhead per module
- ✅ Module tests achieve 80%+ coverage
- ✅ Can create simple addon in < 1 hour

---

## 🔗 Related Repositories

Once this architecture is proven, future projects could use it:
- Example addon template
- Separate campaign module package
- Statistics dashboard addon
- etc.

---

## 📝 Document Maintenance

These documents are the source of truth for the architecture. Update them when:
- Architecture decisions change
- New modules are added
- New hooks are created
- Deployment strategy changes
- New deployment environments are added

---

## ✅ Architecture Sign-Off

**Status**: APPROVED FOR IMPLEMENTATION
**Reviewed By**: [Your Name]
**Date**: November 2, 2025
**Version**: 1.0

**Next**: Begin Phase 1A - Module System Foundation (Week 1)

---

## 📞 Questions?

If you need clarification on any aspect:

1. Check the document index above
2. Search the relevant document section
3. Review the visual guide for diagrams
4. Check quick reference for fast answers
5. Consult the FAQ section

---

**Happy architecting! 🏗️**

This is a solid foundation for building a scalable, extensible forum application that can grow into a TTRPG campaign management platform without architectural rewrites.

Start with Phase 1A and let's build this!
