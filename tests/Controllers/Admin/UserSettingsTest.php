<?php

namespace Controllers\Admin;

use App\Entities\User;
use App\Libraries\Authentication\Actions\TwoFactorAuthEmail;
use App\Models\Factories\UserFactory;
use CodeIgniter\Shield\Authentication\Actions\EmailActivator;
use Exception;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class UserSettingsTest extends TestCase
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
        setting()->forget('Auth.allowRegistration');
        setting()->forget('Auth.passwordValidators');
        setting()->forget('Auth.actions');
        setting()->forget('Auth.sessionConfig');
    }

    /**
     * @throws Exception
     */
    public function testVisitGuest()
    {
        $response = $this->get(route_to('settings-users'));

        $response->assertRedirect();
        // Will return a normal redirect from the Sheild
        $response->assertHeader('Location', url_to('login'));
    }

    public function testViewUserSettings()
    {
        $response = $this->actingAs($this->user)->get(route_to('settings-users'));

        // See some basic headers on the page when it displays properly
        $response->assertSee('Registration');
        $response->assertSee('Password Validators');
    }

    /**
     * @throws Exception
     */
    public function testUserSettings()
    {
        // Check default state on a couple elements
        $this->assertTrue(setting('Auth.allowRegistration'));
        $this->assertSame(30 * DAY, setting('Auth.sessionConfig')['rememberLength']);
        $this->assertTrue(setting('Auth.sessionConfig')['allowRemembering']);
        $this->assertNull(setting('Auth.actions')['register']);

        // Submit the form with some new values
        $response = $this
            ->withHeaders([csrf_header() => csrf_hash()])
            ->actingAs($this->user)
            ->post('admin/settings/users', [
                'minimumPasswordLength' => 8,
                'allowRegistration'     => 1,
                'email2FA'              => 1,
                'emailActivation'       => 1,
                'allowRemember'         => 1,
                'rememberLength'        => 2 * WEEK,
            ]);

        $response->assertOK();

        // Check that the settings were saved
        $this->assertTrue(setting('Auth.allowRegistration'));
        $this->assertTrue(setting('Auth.sessionConfig')['allowRemembering']);
        $this->assertSame(TwoFactorAuthEmail::class, setting('Auth.actions')['login']);
        $this->assertSame(EmailActivator::class, setting('Auth.actions')['register']);
        $this->assertSame(2 * WEEK, (int) setting('Auth.sessionConfig')['rememberLength']);
    }
}
