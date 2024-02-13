<?php

namespace Controllers\Admin;

use App\Entities\User;
use App\Libraries\Authentication\Actions\Email2FA;
use App\Models\Factories\UserFactory;
use CodeIgniter\Shield\Authentication\Actions\EmailActivator;
use Exception;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class TrustLevelsSettingsTest extends TestCase
{
    protected $seed = TestDataSeeder::class;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = fake(UserFactory::class, [
            'password' => 'secret123',
        ]);
        $this->user = $user;
        $this->user->addGroup('superadmin');

        // Ensure we have a clean slate for the settings
        setting()->forget('TrustLevels.allowedActions');
        setting()->forget('TrustLevels.requirements');
    }

    /**
     * @throws Exception
     */
    public function testVisitGuest()
    {
        $response = $this->get(route_to('settings-trust'));

        $response->assertRedirect();
        // Will return a normal redirect from the Sheild
        $response->assertHeader('Location', url_to('login'));
    }

    public function testViewUserSettings()
    {
        $response = $this->actingAs($this->user)->get(route_to('settings-trust'));

        // See some basic headers on the page when it displays properly
        $response->assertSee('Trust Levels Settings');
        $response->assertSee('Level 0');
    }

    /**
     * @throws Exception
     */
    public function testUserSettings()
    {
        // Check default state on a couple elements
        $this->assertFalse(in_array('flag', setting('TrustLevels.allowedActions')[0]));
        $this->assertTrue(in_array('attach', setting('TrustLevels.allowedActions')[1]));
        $this->assertEquals(setting('TrustLevels.requirements')[1]['new-threads'], 5);

        // Submit the form with some new values
        $response = $this
            ->withHeaders([csrf_header() => csrf_hash()])
            ->actingAs($this->user)
            ->post('admin/settings/trust-levels', [
                'trust' => [
                    0 => ['flag' => 1],
                    1 => []
                ],
                'requirements' => [
                    1 => ['new-threads' => 15],
                ],
            ]);

        $response->assertOK();

        // Check that the settings were saved
        $this->assertTrue(in_array('flag', setting('TrustLevels.allowedActions')[0]));
        $this->assertFalse(in_array('attach', setting('TrustLevels.allowedActions')[1]));
        $this->assertEquals(setting('TrustLevels.requirements')[1]['new-threads'], 15);
    }
}
