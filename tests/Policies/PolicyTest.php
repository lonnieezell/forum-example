<?php

use App\Entities\User;
use App\Libraries\Policies\Policy;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Mockery as m;
use Tests\Support\Policies\TestPolicy;

/**
 * @internal
 */
final class PolicyTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace = '';

    public function testCanNoUser()
    {
        $policy = new Policy();

        $this->assertFalse($policy->can('threads.create'));
    }

    public function testCanOnlyUserPermissions()
    {
        $policy = new Policy();
        /** @var User $user */
        $user = fake(UserModel::class);
        $user->addGroup('user');

        $this->assertFalse($user->can('admin.access'));
        $this->assertFalse($policy->withUser($user)->can('admin.access'));

        // Give the user permission and try again
        $user->addPermission('admin.access');

        $this->assertTrue($user->can('admin.access'));
        $this->assertTrue($policy->withUser($user)->can('admin.access'));
    }

    public function testCanSuccess()
    {
        /** @var User $user */
        $user = fake(UserModel::class);
        $user->addGroup('user');

        $policy = m::mock(Policy::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $policy->withPolicy(new TestPolicy());
        $policy->shouldReceive('getPolicy')->andReturn(new TestPolicy());

        $this->assertFalse($policy->withUser($user)->can('test.create'));

        // Add user to the admin group
        $user->addGroup('admin');

        $this->assertTrue($policy->withUser($user)->can('test.create'));
    }

    public function testCanWithArguments()
    {
        /** @var User $user */
        $user = fake(UserModel::class);
        $user->addGroup('user');
        $otherUser = fake(UserModel::class);

        $policy = m::mock(Policy::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $policy->withPolicy(new TestPolicy());
        $policy->shouldReceive('getPolicy')->andReturn(new TestPolicy());

        $this->assertFalse($policy->withUser($user)->can('test.edit', $otherUser));

        // Add user to the admin group
        $user->addGroup('admin');

        $this->assertTrue($policy->withUser($user)->can('test.create'));

        // Now make the target user a superadmin
        $otherUser->addGroup('superadmin');

        $this->assertFalse($policy->withUser($user)->can('test.edit', $otherUser));
    }

    public function testCanWithBefore()
    {
        $user = fake(UserModel::class);
        $user->addGroup('user');

        $policy = m::mock(Policy::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $policy->withPolicy(new TestPolicy());
        $policy->shouldReceive('getPolicy')->andReturn(new TestPolicy());

        $this->assertFalse($policy->withUser($user)->can('test.create'));

        // Add user to the developer group
        $user->addGroup('developer');

        $this->assertTrue($policy->withUser($user)->can('test.create'));
    }

    public function testDeny()
    {
        $policy = new Policy();

        $response = $policy->deny('You are not allowed to create threads.');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode()); // Set by codeigniter-htmx
        $this->assertSame(403, session()->getFlashdata('status'));
        $this->assertSame('You are not allowed to create threads.', session()->getFlashdata('error'));
        // Should have set the HX-Location header
        $this->assertSame(json_encode(['path' => route_to('general-error')]), $response->getHeaderLine('HX-Location'));
    }
}
