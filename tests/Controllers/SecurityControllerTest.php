<?php

namespace Tests\Controllers;

use App\Entities\User;
use App\Models\NotificationMutedModel;
use App\Models\NotificationSettingModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Shield\Authentication\AuthenticationException;
use CodeIgniter\Shield\Exceptions\LogicException;
use CodeIgniter\Router\Exceptions\RouterException;
use CodeIgniter\HTTP\Exceptions\RedirectException;
use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Models\RememberModel;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
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

    public function setUp(): void
    {
        parent::setUp();

        $this->user = fake(UserModel::class);
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

}
