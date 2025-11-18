# Phase 1A: Module System Foundation - Implementation Checklist

**Weeks 1-3: Build the infrastructure that enables everything else**

---

## Overview

Phase 1A is the critical foundation. Everything after this depends on:
- ModuleManager working reliably
- ModuleBootstrapper initializing modules correctly
- Configuration system choosing which modules load
- Ability to have multiple sites running same code

This is where you prove the architecture works.

---

## Week 1: Planning & Setup

### Day 1: Project Structure & Git
- [ ] Create `app/Managers/ModuleManager.php` (empty class)
- [ ] Create `app/Bootstrap/ModuleBootstrapper.php` (empty class)
- [ ] Create `app/Config/Modules.php` (empty config)
- [ ] Create `modules/` directory structure
- [ ] Create `modules-campaign/` directory structure
- [ ] Create `config/forum.php` (empty)
- [ ] Create `config/campaign.php` (empty)
- [ ] Create git branch `feature/module-system`

**Deliverable**: Clean directory structure ready for code

---

### Day 2-3: Design & Documentation
- [ ] Read ADDON_SYSTEM_PRD.md section: "Module System Infrastructure"
- [ ] Design ModuleManager class interface (methods, parameters)
- [ ] Design Module structure (what files each module needs)
- [ ] Design ModuleServiceProvider base class
- [ ] Document bootstrap sequence
- [ ] Create detailed class diagrams

**Deliverable**: Design documents + class specifications

---

### Day 4: Create Base Classes
- [ ] Create `app/Support/ModuleServiceProvider.php` (abstract base)
- [ ] Define interface for module manifest
- [ ] Create module configuration interface
- [ ] Create route registration interface

```php
abstract class ModuleServiceProvider
{
    abstract public function register(): void;
    public function boot(): void {}
    public function getRoutes(): void {}
    public function getPermissions(): array { return []; }
    public function getHooks(): array { return []; }
}
```

**Deliverable**: Base classes that modules will extend

---

### Day 5: Test Setup
- [ ] Create test directory: `tests/Module/`
- [ ] Create PHPUnit test for ModuleManager
- [ ] Create test fixtures (mock modules)
- [ ] Set up test database
- [ ] Verify tests run

**Deliverable**: Test infrastructure ready

---

## Week 2: ModuleManager Implementation

### Day 6: Module Discovery
- [ ] Implement `ModuleManager::discover()` method
  - Scans modules/ and modules-campaign/ directories
  - Reads addon.php manifest files
  - Returns array of discovered modules
- [ ] Test discovery with mock modules
- [ ] Handle missing manifest files gracefully
- [ ] Validate manifest structure

```php
$manager = new ModuleManager();
$discovered = $manager->discover();
// Returns: ['core-auth' => [...], 'core-discussions' => [...], ...]
```

**Deliverable**: Module discovery working

---

### Day 7: Module Loading
- [ ] Implement `ModuleManager::load(array $modules)` method
  - Takes array of module names to load
  - Validates dependencies
  - Throws clear errors for issues
  - Returns loaded module instances
- [ ] Test with valid/invalid module lists
- [ ] Dependency resolution logic
- [ ] Circular dependency detection

```php
$manager->load(['core-auth', 'core-discussions']);
// Loads both, verifies no conflicts
```

**Deliverable**: Module loading working

---

### Day 8: Service Container Integration
- [ ] Implement module service registration
  - Call `register()` on each module provider
  - All modules registered in service container
  - Services accessible to other modules
- [ ] Test service availability
- [ ] Test service isolation

```php
$manager->registerServices();
// Each module can now access shared services
```

**Deliverable**: Services available from all modules

---

### Day 9: Lifecycle Management
- [ ] Implement `ModuleManager::enable()` method
- [ ] Implement `ModuleManager::disable()` method
- [ ] Create `ModuleManager::isEnabled()` method
- [ ] Store enabled/disabled state in config cache
- [ ] Test enable/disable functionality

**Deliverable**: Module lifecycle working

---

### Day 10: Testing Week 2
- [ ] Write unit tests for discovery
- [ ] Write unit tests for loading
- [ ] Write unit tests for dependency validation
- [ ] Write integration tests for full workflow
- [ ] Achieve 80%+ code coverage for ModuleManager

**Deliverable**: ModuleManager fully tested and working

---

## Week 3: Bootstrap & Integration

### Day 11: ModuleBootstrapper
- [ ] Create `ModuleBootstrapper` class
  - Reads config (forum.php or campaign.php)
  - Discovers and loads configured modules
  - Calls boot() on each module
  - Registers routes from all modules
- [ ] Integrate into application bootstrap process
- [ ] Test with both forum and campaign configs

```php
$bootstrapper = new ModuleBootstrapper(config('Modules'));
$bootstrapper->bootstrap();
// All modules loaded and ready
```

**Deliverable**: Bootstrap process working

---

### Day 12: Route Registration
- [ ] Implement route loading from modules
- [ ] Each module's Routes.php loaded
- [ ] Routes prefixed with module namespace
- [ ] Test route availability
- [ ] Verify no route conflicts

```php
// modules/core-discussions/config/Routes.php
$routes->get('discussions', 'DiscussionController::list', ['as' => 'discussions.list']);

// Becomes available as 'discussions.list' route
```

**Deliverable**: Routes from all modules available

---

### Day 13: Configuration System
- [ ] Create `config/forum.php` with module list
- [ ] Create `config/campaign.php` with module list
- [ ] Create logic to load correct config based on entry point
- [ ] Test configuration loading
- [ ] Verify correct modules load per config

```php
// config/forum.php
return ['modules' => ['core-auth', 'core-discussions', ...]]

// config/campaign.php
return ['modules' => ['core-auth', 'core-discussions', ..., 'core-campaigns', ...]]
```

**Deliverable**: Configuration system working

---

### Day 14: Entry Points
- [ ] Update `public/index.php` to be forum entry
  - Sets SITE_TYPE = 'forum'
  - Loads config/forum.php
- [ ] Create `public/index-campaign.php` as campaign entry
  - Sets SITE_TYPE = 'campaign'
  - Loads config/campaign.php
- [ ] Test both entry points load correct modules
- [ ] Verify clean initialization both ways

**Deliverable**: Both sites load independently

---

### Day 15: End-to-End Testing
- [ ] Create test suite combining everything
- [ ] Test forum site loads with 8 modules
- [ ] Test campaign site loads with 13 modules
- [ ] Test forum functionality works
- [ ] Test no campaign features in forum config
- [ ] Test all campaign features in campaign config
- [ ] Performance testing (module load time)
- [ ] Memory usage testing

**Deliverable**: Phase 1A complete and tested

---

## Key Decisions to Make

### 1. Module Discovery
- **Decision**: Scan both `modules/` and `modules-campaign/` or separate?
- **Recommendation**: Separate directories for clarity
- **Impact**: Directory structure, bootstrapper complexity

### 2. Configuration Source
- **Decision**: Store module list in `config/forum.php` or database?
- **Recommendation**: config/ files (simpler, versioned)
- **Impact**: How easily you can change modules

### 3. Service Isolation
- **Decision**: Shared service container or per-module?
- **Recommendation**: Shared (simpler, modules can communicate)
- **Impact**: Module dependency complexity

### 4. Caching
- **Decision**: Cache module list, dependencies, routes?
- **Recommendation**: Cache everything for performance
- **Impact**: Need cache invalidation when modules change

---

## Testing Strategy

### Unit Tests
```
tests/Module/ModuleManagerTest.php
├─ test_discovery_finds_modules
├─ test_load_validates_dependencies
├─ test_enable_disables_modules
├─ test_get_module_returns_instance
└─ test_resolve_dependencies_throws_on_circular

tests/Module/BootstrapperTest.php
├─ test_bootstrap_loads_modules
├─ test_bootstrap_registers_services
├─ test_bootstrap_registers_routes
└─ test_bootstrap_calls_boot_on_modules
```

### Integration Tests
```
tests/Integration/ForumSiteTest.php
├─ test_forum_config_loads_8_modules
├─ test_forum_routes_available
├─ test_campaign_modules_not_loaded
└─ test_forum_pages_render

tests/Integration/CampaignSiteTest.php
├─ test_campaign_config_loads_13_modules
├─ test_all_routes_available
├─ test_forum_features_still_work
└─ test_campaign_pages_render
```

---

## Success Criteria for Phase 1A

### Must Have ✅
- [ ] ModuleManager discovers modules
- [ ] ModuleManager loads specified modules only
- [ ] ModuleBootstrapper initializes modules correctly
- [ ] Forum config loads 8 modules
- [ ] Campaign config loads 13 modules
- [ ] Entry points work correctly
- [ ] Routes from all modules available
- [ ] Services initialized from all modules
- [ ] No errors loading either configuration
- [ ] Tests pass with 80%+ coverage

### Should Have 🎯
- [ ] Performance < 100ms module load
- [ ] Clear error messages for configuration issues
- [ ] Dependency validation prevents bad configs
- [ ] Cache system improves subsequent loads
- [ ] Documentation of module interface

### Nice to Have 💎
- [ ] CLI command to list loaded modules
- [ ] CLI command to test module loading
- [ ] Debug toolbar showing loaded modules
- [ ] Web dashboard showing module status

---

## Common Pitfalls to Avoid

### ❌ Don't
- Make ModuleManager do too much (keep it simple)
- Hardcode module names anywhere (use config)
- Skip dependency validation (test it thoroughly)
- Forget to test performance (load time matters)
- Ignore error messages (make them helpful)

### ✅ Do
- Keep ModuleManager focused on loading/managing
- Use abstract base class for consistency
- Validate everything in tests
- Measure performance early
- Plan for caching early

---

## Dependencies

Phase 1A depends on:
- ✅ PHP 8.4+
- ✅ CodeIgniter 4.5+
- ✅ Existing service container

Phase 1A enables:
- ✅ Phase 1B (refactoring forum into modules)
- ✅ Phase 1C (building campaign modules)
- ✅ Phase 2 (addon system)

---

## Deployment Considerations

### Development
```bash
# Run forum site
php spark serve --host=localhost --port=8080
# Loads config/forum.php

# Run campaign site (different shell/port)
php spark serve --host=localhost --port=8081 --define=SITE_TYPE=campaign
# Loads config/campaign.php
```

### Testing
```bash
# Test both configurations
./vendor/bin/phpunit tests/Integration/ForumSiteTest.php
./vendor/bin/phpunit tests/Integration/CampaignSiteTest.php
```

### Production
```
# Same code, different entry points
forum.example.com → public/index.php
campaigns.example.com → public/index-campaign.php
```

---

## Checklist Completion

Track completion:

```
WEEK 1
- [ ] Days 1-5 complete: Project structure, design, test setup

WEEK 2
- [ ] Days 6-10 complete: ModuleManager fully implemented

WEEK 3
- [ ] Days 11-15 complete: Bootstrap integration, end-to-end tests

PHASE 1A
- [ ] All success criteria met
- [ ] 80%+ code coverage
- [ ] Both sites loading correctly
- [ ] Ready for Phase 1B
```

---

## Next Phase

Once Phase 1A is complete:

**Phase 1B: Refactor Forum to Modules** (Weeks 4-6)
- Move core forum code into modules/core-*
- Create forum theme
- Verify forum functionality preserved

See **PHASE_1_IMPLEMENTATION_GUIDE.md** for details.

---

## Questions?

If you get stuck:
1. Check ADDON_SYSTEM_PRD.md → Module System Infrastructure
2. Review ARCHITECTURE_VISUAL_GUIDE.md → Module Loading
3. Check PHASE_1_IMPLEMENTATION_GUIDE.md for more details
4. Reference ARCHITECTURE_QUICK_REFERENCE.md

---

**Ready to build? Let's go! 🚀**

Start with Day 1 and follow the checklist. This is the foundation everything else depends on.
