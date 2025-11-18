# Architecture Decision Record

**Decision Made**: Modular Monolith with Multi-Site Configuration
**Date**: November 2, 2025
**Status**: APPROVED FOR IMPLEMENTATION
**Stakeholder**: Development Team

---

## Problem Statement

How can we build a forum application that also serves as a TTRPG campaign management platform, both from the same codebase without duplication?

---

## Options Considered

### Option 1: Monolithic Forum (Original Plan)
```
Build Forum
├─ Controllers
├─ Models
├─ Views
└─ When campaigns needed: Add campaign features as addons
```

**Pros**: Simple at first
**Cons**: Campaign features feel tacked on, hard to disable either, tight coupling

### Option 2: Separate Codebases
```
forum-code (Git Repo 1)
campaign-code (Git Repo 2) - Copy of forum + campaign code
```

**Pros**: Independent evolution
**Cons**: Massive duplication, painful to fix bugs in both, team confusion

### Option 3: Module-Based Monolith ✅ CHOSEN
```
Core Modules
├─ core-auth
├─ core-discussions
├─ core-posts
├─ core-moderation
├─ core-reactions
├─ core-tagging
├─ core-notifications
├─ core-user-profiles
├─ core-campaigns (optional)
├─ core-wiki (optional)
├─ core-maps (optional)
├─ core-characters (optional)
└─ core-sessions (optional)

Forum Config: Load 8 core modules + forum theme
Campaign Config: Load 13 modules + campaign theme
```

**Pros**:
- Single codebase
- Both sites equal citizens
- Easy to add third site
- Modular, testable
- Proven pattern
- Future scalability

**Cons**:
- Requires module system infrastructure
- More upfront architecture work
- More documentation

---

## Decision: Module-Based Monolith (Option 3)

### Rationale

1. **Solves Multi-Site Problem Cleanly**
   - Not "forum + campaign addons"
   - Rather "choose which modules to load"

2. **Proven Pattern**
   - WordPress uses plugins (modules)
   - Drupal is all modules
   - nopCommerce uses modules
   - Laravel ecosystem uses packages

3. **Better Long-Term Architecture**
   - Could add third site easily
   - Modules can be extracted to packages
   - Future microservices migration path

4. **No Extra Work**
   - Module system needed anyway for addon system
   - Just extends it for multiple sites

5. **Better Separation**
   - Campaign code doesn't leak into forum
   - Forum code doesn't leak into campaign
   - Clear boundaries for team

---

## Implementation Approach

### Phase 1A: Module System Foundation (Weeks 1-3)
Build infrastructure that enables everything else:
- ModuleManager (discovers and loads modules)
- ModuleBootstrapper (initializes modules)
- Configuration system (forum.php vs campaign.php)
- Entry points (index.php vs index-campaign.php)

### Phase 1B: Refactor Forum (Weeks 4-6)
Extract existing forum code into modules:
- modules/core-discussions/
- modules/core-posts/
- modules/core-moderation/
- modules/core-reactions/
- modules/core-tagging/
- modules/core-notifications/
- modules/core-user-profiles/
- Plus forum-theme

### Phase 1C: Build Campaign Modules (Weeks 8-10)
Create campaign-specific modules:
- modules-campaign/core-campaigns/
- modules-campaign/core-wiki/
- modules-campaign/core-maps/
- modules-campaign/core-characters/
- modules-campaign/core-sessions/

### Phase 1D: Campaign Theme (Weeks 10-11)
Create fantasy-themed UI for campaign site

### Phase 2: Addon System (Weeks 12+)
Build addon discovery, admin UI, example addons

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────┐
│              Single Git Repository                      │
│          forum-example (Same Codebase)                 │
└─────────────────────────────────────────────────────────┘
                    ↙                    ↘
        ┌──────────────────┐   ┌──────────────────┐
        │   FORUM SITE     │   │  CAMPAIGN SITE   │
        ├──────────────────┤   ├──────────────────┤
        │ index.php        │   │ index-campaign.  │
        │ config/forum.php │   │ config/campaign. │
        │ 8 modules        │   │ 13 modules       │
        │ forum-theme      │   │ campaign-theme   │
        └──────────────────┘   └──────────────────┘
                ↓                        ↓
        ┌──────────────────────────────────────┐
        │  Shared Infrastructure               │
        ├──────────────────────────────────────┤
        │  • Database                          │
        │  • Users & Authentication            │
        │  • ModuleManager                     │
        │  • Hook/Event System                 │
        │  • Permission/Policy System          │
        └──────────────────────────────────────┘
```

---

## Key Decisions

### 1. Module Structure
**Decision**: Separate `modules/` (shared) and `modules-campaign/` (campaign-only)
**Reason**: Clear organization, easy to understand which modules are optional

### 2. Configuration Approach
**Decision**: `config/forum.php` lists modules to load
**Reason**: Simple, versioned, no database dependency

### 3. Entry Points
**Decision**: `public/index.php` (forum) and `public/index-campaign.php` (campaign)
**Reason**: Clear separation, easy to deploy separately if needed

### 4. Database
**Decision**: Single shared database for both sites
**Reason**: Users exist in both, permissions control visibility

### 5. Themes
**Decision**: Separate theme directories (forum-theme, campaign-theme)
**Reason**: Different UIs for same modules, easy to customize per site

---

## Risks & Mitigations

| Risk | Mitigation |
|------|-----------|
| **Module system too complex** | Start with simple implementation, add features incrementally |
| **Performance overhead** | Cache module list, route registry, measure early |
| **Team confusion** | Excellent documentation (done), clear examples |
| **Breaking changes in refactor** | Git branch, thorough testing, staged migration |
| **Campaign modules missing features** | Clear specs, testing strategy, weekly review |

---

## Rollback Plan

If architecture doesn't work:

1. **Stage 1** (High Risk): Module system doesn't load correctly
   - Solution: Revert to pre-refactor code, keep learnings
   - Effort: ~3 weeks (just Phase 1A)

2. **Stage 2** (Medium Risk): Refactoring breaks forum
   - Solution: Use git tags, revert to working state
   - Effort: ~6 weeks (Phase 1A + 1B)

3. **Stage 3** (Low Risk): Campaign modules have issues
   - Solution: Disable campaign modules, keep forum working
   - Effort: Minimal (just disable modules)

**Confidence Level**: HIGH - This is proven architecture

---

## Success Criteria

✅ **Architecture**
- [ ] ModuleManager works reliably
- [ ] Forum loads with 8 modules
- [ ] Campaign loads with 13 modules
- [ ] No module coupling

✅ **Implementation**
- [ ] Phase 1A complete (3 weeks)
- [ ] Phase 1B complete (3 weeks)
- [ ] Phase 1C complete (3 weeks)
- [ ] Phase 1D complete (2 weeks)
- [ ] Both sites in production (11 weeks)

✅ **Quality**
- [ ] 80%+ test coverage
- [ ] < 100ms module load time
- [ ] Zero performance regression
- [ ] All hooks documented

✅ **Documentation**
- [ ] Complete PRD
- [ ] Implementation guide
- [ ] Example addons
- [ ] Architecture documentation

---

## Budget Impact

### Development Time
- **Phase 1A** (Module System): 3 weeks
- **Phase 1B** (Forum Refactor): 3 weeks
- **Phase 1C** (Campaign Modules): 3 weeks
- **Phase 1D** (Campaign Theme): 2 weeks
- **Phase 2** (Addon System): 2+ weeks
- **Total**: 11-13 weeks

### Compared to Original Plan
- Original: Forum (4 weeks) + Campaign bolted on (4 weeks) = 8 weeks + technical debt
- New: Modular (11 weeks) + addon system included + no technical debt

**Net**: +3 weeks, but better architecture, included addon system, no future refactoring needed

---

## Alternatives Rejected

### Alternative: "Forum First, Then Addons for Campaign"
**Rejected because**:
- Campaign features feel secondary
- Tight coupling makes maintenance hard
- Can't easily disable campaign features for forum-only users
- No path to multiple sites

### Alternative: "Microservices from Day One"
**Rejected because**:
- Too complex for current scale
- Operational overhead
- Can revisit when scaling requires it
- Modular monolith provides migration path

### Alternative: "Separate Deployments of Same Code"
**Rejected because**:
- Doesn't solve the multi-site problem
- Still monolithic within each deployment
- No benefit over modular monolith

---

## Future Expansion

This architecture enables:

### Year 1
- Forum site running
- Campaign site running
- Addon system working

### Year 2
- Third site (Wiki Hub)
- Fourth site (Character Gallery)
- Extract popular modules to packages

### Year 3+
- Microservices if scaling requires
- Separate deployments per site
- Multi-region deployment

---

## Approval Sign-Off

**Architecture**: APPROVED
**Approach**: APPROVED
**Timeline**: APPROVED
**Documentation**: COMPLETE

**Ready to implement**: YES ✅

---

## Next Steps

1. **Week of Nov 2**: Review & approve this document
2. **Week of Nov 9**: Begin Phase 1A (Module System)
3. **Week of Nov 30**: Phase 1A complete, begin Phase 1B
4. **Week of Dec 21**: Phase 1B complete, begin Phase 1C
5. **Week of Jan 11**: Phase 1C complete, begin Phase 1D
6. **Week of Jan 25**: Phase 1D complete, Phase 2 optional
7. **Week of Feb 8**: Both sites in production

---

## References

Related Documents:
- MULTI_SITE_STRATEGY.md (Strategy explanation)
- ARCHITECTURE_VISUAL_GUIDE.md (Visual architecture)
- ADDON_SYSTEM_PRD.md (Complete specifications)
- PHASE_1A_CHECKLIST.md (Implementation checklist)

---

**Decision Made**: Modular Monolith Architecture
**Approved By**: [Your Name]
**Date**: November 2, 2025
**Effective Date**: Immediately - Begin Phase 1A

**LET'S BUILD THIS! 🚀**
