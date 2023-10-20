<?php

namespace Tests\Controllers;

use App\Models\Factories\UserFactory;
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
        $response->assertSeeElement('h2');
        $response->assertSee('My Notifications');

        $this->seeInDatabase('notification_settings', [
            'user_id'          => $user->id,
            'email_thread'     => 1,
            'email_post'       => 0,
            'email_post_reply' => 0,
        ]);
    }
}
