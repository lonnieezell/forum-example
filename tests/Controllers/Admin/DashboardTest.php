<?php

namespace Controllers\Admin;

use App\Entities\User;
use App\Models\Factories\UserFactory;
use Exception;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class DashboardTest extends TestCase
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
    public function testVisitGuest()
    {
        $response = $this->get(route_to('admin-dashboard'));

        $response->assertRedirect();
        // Will return a normal redirect from the Sheild
        $response->assertHeader('Location', url_to('login'));
    }

    /**
     * @throws Exception
     */
    public function testDashboard()
    {
        $response = $this->actingAs($this->user)->get(route_to('admin-dashboard'));

        $response->assertSee('App and Server Statistics');
        $response->assertSee('Forum Statistics');
    }
}
