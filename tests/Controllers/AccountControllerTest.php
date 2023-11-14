<?php

namespace Tests\Controllers;

use App\Models\Factories\UserFactory;
use CodeIgniter\Config\Factory;
use Config\Services;
use Exception;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class AccountControllerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testShowPosts()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this->actingAs($user)->get('account/posts');

        $response->assertOK();
        $response->assertSeeElement('h2');
        $response->assertSee('My Posts');
    }

    /**
     * @throws Exception
     */
    public function testShowNotifications()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this->actingAs($user)->get('account/notifications');

        $response->assertOK();
        $response->assertSeeElement('h2');
        $response->assertSee('My Notifications');
    }

    public function testUpdateNotifications()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this
            ->withHeaders([csrf_header() => csrf_hash()])
            ->actingAs($user)->post('account/notifications', [
                'email_thread'     => 1,
                'email_post'       => 0,
                'email_post_reply' => 0,
            ]);

        $response->assertOK();
        $response->assertSeeElement('form');

        $this->seeInDatabase('notification_settings', [
            'user_id'          => $user->id,
            'email_thread'     => 1,
            'email_post'       => 0,
            'email_post_reply' => 0,
        ]);
    }

    public function testViewProfile()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this->actingAs($user)->get('account/profile');

        $response->assertOK();
        $response->assertSeeElement('h2');
        $response->assertSee('My Profile');
        $response->assertSee('testuser');
    }

    public function testSaveProfile()
    {
        $storage = service('storage')->setDefaultDisk('test');
        Services::injectMock('storage', $storage);

        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this
            ->withHeaders([csrf_header() => csrf_hash()])
            ->actingAs($user)->post('account/profile', [
                'name'      => 'Test User',
                'handed'    => 'left',
                'country'   => 'US',
                'website'   => 'https://example.com',
                'location'  => 'New York',
                'company'   => 'Example, Inc.',
                'signature' => 'Test signature',
            ]);

        $response->assertOK();
        $response->assertSeeElement('legend');
        $response->assertSee('Personal');

        $this->seeInDatabase('users', [
            'id'        => $user->id,
            'name'      => 'Test User',
            'handed'    => 'left',
            'country'   => 'US',
            'website'   => 'https://example.com',
            'location'  => 'New York',
            'company'   => 'Example, Inc.',
            'signature' => 'Test signature',
        ]);
    }
}
