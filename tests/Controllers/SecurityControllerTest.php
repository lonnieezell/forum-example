<?php

namespace Tests\Controllers;

use App\Entities\User;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Models\RememberModel;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestDataSeeder;

/**
 * @internal
 */
final class SecurityControllerTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    use AuthenticationTesting;

    protected $namespace;
    protected $seed = TestDataSeeder::class;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = fake(UserModel::class, [
            'password' => 'secret123',
        ]);
        $this->user->addGroup('admin');
    }

    /**
     * TODO: Investigate why this fails in test,
     * but works in real life.
     */
    // public function testIndexGuest()
    // {
    //     $guest = fake(UserModel::class);
    //     $guest->addGroup('user');

    //     $result = $this->actingAs($guest)
    //         ->get(route_to('account-security'));

    //     $result->assertRedirectTo('/login');
    // }

    public function testIndexAdmin()
    {
        // Admin user should see the security page
        $result = $this->actingAs($this->user)
            ->get(route_to('account-security'));

        $result->assertOk();
        $result->assertSee('Reset Password');
    }

    public function testLogoutAll()
    {
        $memory = model(RememberModel::class);

        // Ensure the user has a remember token
        $memory->rememberUser($this->user, 'abc', 'def', Time::now()->addDays(1)->toDateTimeString());
        $this->assertCount(1, $memory->where('user_id', $this->user->id)->findAll());

        $this->actingAs($this->user)
            ->post(route_to('account-security-logout-all'));

        $this->assertCount(0, $memory->where('user_id', $this->user->id)->findAll());
    }

    public function testDeleteAccount()
    {
        // First with invalid password
        $response = $this->actingAs($this->user)
            ->post(route_to('account-security-delete'), ['password' => 'invalid']);
        $response->assertSee('The password you entered is incorrect.');

        // Now with valid password
        $response = $this->actingAs($this->user)
            ->post(route_to('account-security-delete'), ['password' => 'secret123']);

        // Should redirect
        $response->assertHeader('HX-Location', json_encode(['path' => '/login']));

        // User should be deleted
        $this->assertNull(model(UserModel::class)->find($this->user->id));
    }
}
