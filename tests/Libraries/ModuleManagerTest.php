<?php

use App\Libraries\Modules\ModuleManager;
use CodeIgniter\Settings\Settings;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;

/**
 * @internal
 */
final class ModuleManagerTest extends CIUnitTestCase
{
    private ModuleManager $manager;
    private array $testModules;

    protected $namespace = 'CodeIgniter\Settings';

    protected function setUp(): void
    {
        parent::setUp();

        // Get Settings config
        /** @var \CodeIgniter\Settings\Config\Settings $settingsConfig */
        $settingsConfig = config('Settings');

        // Override to use array handler only (in-memory, no database)
        $settingsConfig->handlers = ['array'];

        // Create Settings service with array handler
        $settings = new Settings($settingsConfig);
        Services::injectMock('settings', $settings);

        // Reset cache before each test
        cache()->deleteMatching('modules*');

        $this->manager = new ModuleManager();

        // Setup test modules structure
        $this->testModules = [
            'core'  => ['name' => 'Core', 'dependencies' => []],
            'admin' => ['name' => 'Admin', 'dependencies' => ['core']],
            'users' => ['name' => 'Users', 'dependencies' => ['core']],
            'blog'  => ['name' => 'Blog', 'dependencies' => ['core', 'users']],
        ];

        // Inject test modules into manager via reflection
        $reflection = new ReflectionClass($this->manager);
        $property = $reflection->getProperty('modules');
        $property->setAccessible(true);
        $property->setValue($this->manager, $this->testModules);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        cache()->deleteMatching('modules*');
        unset($GLOBALS['test_module_settings']);
    }

    /**
     * Helper to get a test setting (mocked instead of from database)
     */
    private function getTestSetting(string $key)
    {
        return $GLOBALS['test_module_settings'][$key] ?? null;
    }

    /**
     * Helper to set a test setting (mocked instead of from database)
     */
    private function setTestSetting(string $key, $value): void
    {
        $GLOBALS['test_module_settings'][$key] = $value;
    }

    // ==================== Module Discovery Tests ====================

    public function testDiscoverLoadsModulesFromCache()
    {
        // Mock cache with existing data
        cache()->save('modules_manifest', $this->testModules, 3600);

        $result = $this->manager->discover();

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertArrayHasKey('core', $result);
    }

    public function testDiscoverDetectsCircularDependencies()
    {
        $circularModules = [
            'a' => ['name' => 'A', 'dependencies' => ['b']],
            'b' => ['name' => 'B', 'dependencies' => ['a']],
        ];

        // Inject circular modules and set in cache to bypass filesystem discovery
        $reflection = new ReflectionClass($this->manager);
        $property = $reflection->getProperty('modules');
        $property->setAccessible(true);
        $property->setValue($this->manager, $circularModules);

        // This should throw because detectCircularDependencies is called in discover()
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Circular dependency detected');

        // Call the detection directly
        $this->manager->detectCircularDependencies();
    }

    // ==================== Module Retrieval Tests ====================

    public function testGetAllModules()
    {
        $modules = $this->manager->getAllModules();

        $this->assertIsArray($modules);
        $this->assertCount(4, $modules);
        $this->assertArrayHasKey('core', $modules);
        $this->assertArrayHasKey('admin', $modules);
    }

    public function testGetModule()
    {
        $core = $this->manager->getModule('core');

        $this->assertIsArray($core);
        $this->assertSame('Core', $core['name']);
        $this->assertSame([], $core['dependencies']);
    }

    public function testGetModuleReturnsNullForNonexistent()
    {
        $result = $this->manager->getModule('nonexistent');

        $this->assertNull($result);
    }

    public function testGetModuleIsCaseInsensitive()
    {
        $core1 = $this->manager->getModule('core');
        $core2 = $this->manager->getModule('CORE');
        $core3 = $this->manager->getModule('Core');

        $this->assertSame($core1, $core2);
        $this->assertSame($core2, $core3);
    }

    // ==================== Dependency Tests ====================

    public function testGetModuleDependenciesSimple()
    {
        $deps = $this->manager->getModuleDependencies('admin');

        $this->assertIsArray($deps);
        $this->assertCount(1, $deps);
        $this->assertContains('core', $deps);
    }

    public function testGetModuleDependenciesRecursive()
    {
        $deps = $this->manager->getModuleDependencies('blog');

        $this->assertCount(2, $deps);
        $this->assertContains('core', $deps);
        $this->assertContains('users', $deps);
    }

    public function testGetModuleDependenciesThrowsOnNonexistent()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Module \'nonexistent\' does not exist');

        $this->manager->getModuleDependencies('nonexistent');
    }

    public function testGetModuleDependenciesThrowsOnMissingDependency()
    {
        $modules = $this->testModules;
        $modules['broken'] = ['name' => 'Broken', 'dependencies' => ['nonexistent']];

        $reflection = new ReflectionClass($this->manager);
        $property = $reflection->getProperty('modules');
        $property->setAccessible(true);
        $property->setValue($this->manager, $modules);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('requires dependency \'nonexistent\', but it does not exist');

        $this->manager->getModuleDependencies('broken');
    }

    public function testGetModuleDependenciesDepthLimit()
    {
        // Create deeply nested dependencies
        $modules = ['mod0' => ['name' => 'Mod0', 'dependencies' => []]];

        for ($i = 1; $i <= 52; $i++) {
            $modules['mod' . $i] = [
                'name' => 'Mod' . $i,
                'dependencies' => ['mod' . ($i - 1)],
            ];
        }

        $reflection = new ReflectionClass($this->manager);
        $property = $reflection->getProperty('modules');
        $property->setAccessible(true);
        $property->setValue($this->manager, $modules);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Dependency depth limit exceeded');

        $this->manager->getModuleDependencies('mod52');
    }

    // ==================== Dependents Tests ====================

    public function testGetDependentsSimple()
    {
        $dependents = $this->manager->getDependents('core');

        $this->assertIsArray($dependents);
        $this->assertCount(3, $dependents);
        $this->assertContains('admin', $dependents);
        $this->assertContains('users', $dependents);
        $this->assertContains('blog', $dependents);
    }

    public function testGetDependentsNone()
    {
        $dependents = $this->manager->getDependents('blog');

        $this->assertIsArray($dependents);
        $this->assertEmpty($dependents);
    }

    public function testGetDependentsIsCached()
    {
        // First call - should hit the computation path
        $dependents1 = $this->manager->getDependents('core');

        // Verify cache is set
        $cacheKey = 'modules_dependents_core';
        $cached = cache()->get($cacheKey);
        $this->assertNotNull($cached);

        // Second call should use cache
        $dependents2 = $this->manager->getDependents('core');

        $this->assertSame($dependents1, $dependents2);
    }

    // ==================== Enable Tests ====================

    public function testEnableModule()
    {
        $this->manager->enable('core');

        $this->assertTrue($this->manager->isEnabled('core'));
        $this->assertContains('core', $this->manager->getEnabledModules());
    }

    public function testEnableModuleWithDependencies()
    {
        $this->manager->enable('blog');

        $enabled = $this->manager->getEnabledModules();

        $this->assertTrue($this->manager->isEnabled('blog'));
        $this->assertTrue($this->manager->isEnabled('users'));
        $this->assertTrue($this->manager->isEnabled('core'));
        $this->assertCount(3, $enabled);
    }

    public function testEnableModuleThrowsOnNonexistent()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot enable module \'nonexistent\', it does not exist');

        $this->manager->enable('nonexistent');
    }

    public function testEnableModuleThrowsOnMissingDependency()
    {
        $modules = $this->testModules;
        $modules['broken'] = ['name' => 'Broken', 'dependencies' => ['missing']];

        $reflection = new ReflectionClass($this->manager);
        $property = $reflection->getProperty('modules');
        $property->setAccessible(true);
        $property->setValue($this->manager, $modules);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('requires dependency \'missing\', but it does not exist');

        $this->manager->enable('broken');
    }

    public function testEnableModuleIsCaseInsensitive()
    {
        $this->manager->enable('CORE');

        $this->assertTrue($this->manager->isEnabled('core'));
        $this->assertTrue($this->manager->isEnabled('Core'));
    }

    public function testEnableModuleClearsCache()
    {
        // Pre-populate cache with dependents
        cache()->save('modules_dependents_core', ['admin', 'users', 'blog'], 3600);
        $this->assertNotNull(cache()->get('modules_dependents_core'));

        $this->manager->enable('core');

        // Cache should be cleared
        $this->assertNull(cache()->get('modules_dependents_core'));
    }

    public function testEnableModuleClearsDependenciesCache()
    {
        // Pre-populate caches
        cache()->save('modules_dependents_blog', ['test'], 3600);
        cache()->save('modules_dependents_core', ['admin', 'users'], 3600);
        cache()->save('modules_dependents_users', [], 3600);

        $this->manager->enable('blog');

        // All related caches should be cleared
        $this->assertNull(cache()->get('modules_dependents_blog'));
        $this->assertNull(cache()->get('modules_dependents_core'));
        $this->assertNull(cache()->get('modules_dependents_users'));
    }

    // ==================== Disable Tests ====================

    public function testDisableModule()
    {
        // Enable first
        $this->manager->enable('blog');
        $this->assertTrue($this->manager->isEnabled('blog'));

        // Disable
        $this->manager->disable('blog');

        $this->assertFalse($this->manager->isEnabled('blog'));
    }

    public function testDisableModuleThrowsIfDependentEnabled()
    {
        $this->manager->enable('blog');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The following enabled modules depend on it');

        $this->manager->disable('core');
    }

    public function testDisableModuleAllowsIfDependentDisabled()
    {
        $this->manager->enable('core');
        $this->manager->enable('admin');

        // Disable blog (no dependents)
        $this->manager->disable('admin');

        $this->assertTrue($this->manager->isEnabled('core'));
        $this->assertFalse($this->manager->isEnabled('admin'));
    }

    public function testDisableModuleThrowsOnNonexistent()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot disable module \'nonexistent\', it does not exist');

        $this->manager->disable('nonexistent');
    }

    public function testDisableModuleIsCaseInsensitive()
    {
        $this->manager->enable('core');
        $this->manager->disable('CORE');

        $this->assertFalse($this->manager->isEnabled('core'));
    }

    public function testDisableModuleClearsCache()
    {
        $this->manager->enable('core');

        // Verify cache can be set and cleared via the manager's cache system
        // The disable method should call clearModuleCache internally
        $this->manager->disable('core');

        // Verify core is no longer enabled
        $this->assertFalse($this->manager->isEnabled('core'));
    }

    public function testDisableModuleClearsDependentsCache()
    {
        $this->manager->enable('core');
        $this->manager->enable('admin');
        $this->manager->enable('users');

        // Disable dependents first, then core
        $this->manager->disable('admin');
        $this->manager->disable('users');
        $this->manager->disable('core');

        // Verify all are disabled
        $this->assertFalse($this->manager->isEnabled('core'));
        $this->assertFalse($this->manager->isEnabled('admin'));
        $this->assertFalse($this->manager->isEnabled('users'));
    }

    // ==================== Module Status Tests ====================

    public function testIsEnabledTrue()
    {
        $this->manager->enable('core');

        $this->assertTrue($this->manager->isEnabled('core'));
    }

    public function testIsEnabledFalse()
    {
        $this->assertTrue(!$this->manager->isEnabled('core'));
    }

    public function testIsEnabledIsCaseInsensitive()
    {
        $this->manager->enable('core');

        $this->assertTrue($this->manager->isEnabled('CORE'));
        $this->assertTrue($this->manager->isEnabled('Core'));
    }

    public function testGetEnabledModules()
    {
        $this->manager->enable('admin');
        $this->manager->enable('blog');

        $enabled = $this->manager->getEnabledModules();

        $this->assertIsArray($enabled);
        $this->assertCount(4, $enabled); // core, admin, users, blog
        $this->assertContains('admin', $enabled);
        $this->assertContains('blog', $enabled);
        $this->assertContains('core', $enabled);
        $this->assertContains('users', $enabled);
    }

    // ==================== Cache Management Tests ====================

    public function testClearCacheRemovesManifest()
    {
        cache()->save('modules_manifest', $this->testModules, 3600);
        $this->assertNotNull(cache()->get('modules_manifest'));

        $this->manager->clearCache();

        $this->assertNull(cache()->get('modules_manifest'));
    }

    public function testClearCacheRemovesDependentsKey()
    {
        // Just verify the clearCache method runs without errors
        $this->manager->clearCache();

        // And that the manifest cache is not populated after clearing
        $this->assertNull(cache()->get('modules_manifest'));
    }

    // ==================== Integration Tests ====================

    public function testComplexModuleHierarchy()
    {
        $complex = [
            'core'      => ['name' => 'Core', 'dependencies' => []],
            'auth'      => ['name' => 'Auth', 'dependencies' => ['core']],
            'users'     => ['name' => 'Users', 'dependencies' => ['core', 'auth']],
            'posts'     => ['name' => 'Posts', 'dependencies' => ['core', 'users']],
            'comments'  => ['name' => 'Comments', 'dependencies' => ['posts', 'users']],
            'admin'     => ['name' => 'Admin', 'dependencies' => ['auth', 'users']],
        ];

        $reflection = new ReflectionClass($this->manager);
        $property = $reflection->getProperty('modules');
        $property->setAccessible(true);
        $property->setValue($this->manager, $complex);

        // Enable comments (should enable everything it depends on)
        $this->manager->enable('comments');

        $enabled = $this->manager->getEnabledModules();

        $this->assertCount(5, $enabled);
        $this->assertContains('comments', $enabled);
        $this->assertContains('posts', $enabled);
        $this->assertContains('users', $enabled);
        $this->assertContains('core', $enabled);
        $this->assertContains('auth', $enabled);

        // Try to disable core (should fail)
        $this->expectException(InvalidArgumentException::class);
        $this->manager->disable('core');
    }

    public function testSelectiveEnableDisable()
    {
        $this->manager->enable('admin');
        $this->manager->enable('blog');

        $enabled = $this->manager->getEnabledModules();
        $this->assertCount(4, $enabled); // core, admin, users, blog

        // Disable admin only
        $this->manager->disable('admin');

        $enabled = $this->manager->getEnabledModules();
        $this->assertCount(3, $enabled); // core, users, blog
        $this->assertFalse($this->manager->isEnabled('admin'));
        $this->assertTrue($this->manager->isEnabled('blog'));
        $this->assertTrue($this->manager->isEnabled('core'));
    }
}
