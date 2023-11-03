<?php

namespace Tests\Controllers;

use App\Entities\User;
use App\Models\Factories\UserFactory;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Models\RememberModel;
use Exception;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class SecurityControllerTest extends TestCase
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
        $this->user->addGroup('admin');
    }

    /**
     * @throws Exception
     */
    public function testIndexGuest()
    {
        $result = $this->get(route_to('account-security'));

        $result->assertRedirectTo('/login');
    }

    public function testIndexAdmin()
    {
        // Admin user should see the security page
        $result = $this->actingAs($this->user)
            ->withHeaders([csrf_header() => csrf_hash()])
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
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post(route_to('account-security-logout-all'));

        $this->assertCount(0, $memory->where('user_id', $this->user->id)->findAll());
    }

    public function testDeleteAccount()
    {
        // First with invalid password
        $response = $this->actingAs($this->user)
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post(route_to('account-security-delete'), ['password' => 'invalid']);
        $response->assertSee('The password you entered is incorrect.');

        // Now with valid password
        $response = $this->actingAs($this->user)
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post(route_to('account-security-delete'), ['password' => 'secret123']);

        // Should redirect
        $response->assertHeader('HX-Redirect', url_to('login'));

        // User should be deleted
        $this->assertNull(model(UserModel::class)->find($this->user->id));
    }

    public function testChangePassword()
    {
        // First with invalid password
        $response = $this->actingAs($this->user)
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post(route_to('account-change-password'), [
                'current_password' => 'invalid',
                'password'         => 'alkd9fsdklfj!*',
                'confirm_password' => 'alkd9fsdklfj!*',
            ]);
        $response->assertSee('The password you entered is incorrect.');

        // Now with valid password
        $response = $this->actingAs($this->user)
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post(route_to('account-change-password'), [
                'current_password' => 'secret123',
                'password'         => 'alkd9fsdklfj!*',
                'confirm_password' => 'alkd9fsdklfj!*',
            ]);

        // Should redirect
        $response->assertSee('Your password has been updated.');
    }
}
