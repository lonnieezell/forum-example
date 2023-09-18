<?php

namespace Tests\Controllers;

use App\Models\Factories\UserFactory;
use App\Models\UserModel;
use Exception;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class ThreadControllerTest extends TestCase
{
    protected $seed = TestDataSeeder::class;

    /**
     * @throws Exception
     */
    public function testCanUserSeeTheCreateADiscussionPage()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this->actingAs($user)->get('discussions/new');

        $response->assertOK();
        $response->assertSeeElement('.thread-create');
        $response->assertSee('Create a new thread');
    }

    /**
     * @throws Exception
     */
    public function testCanUserCreateADiscussion()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this->actingAs($user)->post('discussions/new', [
            'title'       => 'A new thread',
            'category_id' => 2,
            'body'        => 'Sample body',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('hx-redirect', site_url(implode('/', [
            'discussions', 'cat-1-sub-category-1', 'a-new-thread',
        ])));

        $this->seeInDatabase('threads', ['title' => 'A new thread']);
    }

    /**
     * @throws Exception
     */
    public function testCanGuestSeeCreateADiscussionPage()
    {
        $response = $this->withHeaders([
            'HX-Request' => 'true',
        ])->get('discussions/new');

        $response->assertHeader('HX-Location', '{"path":"\/display-error"}');
        $response->assertSessionHas('message', 'You are not allowed to create threads.');
        $response->assertSessionHas('status', '403');
    }

    /**
     * @throws Exception
     */
    public function testCanGuestCreateADiscussion()
    {
        $response = $this->withHeaders([
            'HX-Request' => 'true',
        ])->post('discussions/new', [
            'title'       => 'A new thread',
            'category_id' => 2,
            'body'        => 'Sample body',
        ]);

        $response->assertHeader('HX-Location', '{"path":"\/display-error"}');
        $response->assertSessionHas('message', 'You are not allowed to create threads.');
        $response->assertSessionHas('status', '403');
    }

    /**
     * @throws Exception
     */
    public function testCanUserSeeEditPageOfSomeoneElseThread()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this->actingAs($user)->withHeaders([
            'HX-Request' => 'true',
        ])->get('discussions/1/edit');

        $response->assertHeader('HX-Location', '{"path":"\/display-error"}');
        $response->assertSessionHas('message', 'You are not allowed to edit this thread.');
        $response->assertSessionHas('status', '403');
    }

    /**
     * @throws Exception
     */
    public function testCanUserUpdateSomeoneElseThread()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this->actingAs($user)->withHeaders([
            'HX-Request' => 'true',
        ])->withBody(http_build_query([
            'title'       => 'A updated thread',
            'category_id' => 2,
            'body'        => 'Sample updated body',
        ]))->put('discussions/1/edit');

        $response->assertHeader('HX-Location', '{"path":"\/display-error"}');
        $response->assertSessionHas('message', 'You are not allowed to edit this thread.');
        $response->assertSessionHas('status', '403');
    }

    /**
     * @throws Exception
     */
    public function testCanUserSeeEditPageOfHisOwnThread()
    {
        $user     = model(UserModel::class)->find(1);
        $response = $this->actingAs($user)->get('discussions/1/edit');

        $response->assertOK();
        $response->assertSee('Edit the thread', 'div.card-title');
    }

    /**
     * @throws Exception
     */
    public function testCanUserUpdateHisOwnThread()
    {
        $user     = model(UserModel::class)->find(1);
        $response = $this->actingAs($user)->withBody(http_build_query([
            'title'       => 'A updated thread',
            'category_id' => 2,
            'body'        => 'Sample updated body',
        ]))->put('discussions/1/edit');

        $response->assertOK();
        $response->assertSee('A updated thread', 'h3');
        $this->seeInDatabase('threads', ['title' => 'A updated thread']);
    }
}
