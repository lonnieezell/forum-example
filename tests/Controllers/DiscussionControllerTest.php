<?php

namespace Tests\Controllers;

use App\Models\Factories\UserFactory;
use Exception;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class DiscussionControllerTest extends TestCase
{
    protected $seed = TestDataSeeder::class;

    /**
     * @throws Exception
     */
    public function testShowDiscussions()
    {
        $response = $this->get('discussions');

        $response->assertOK();
        $response->assertSeeElement('#discussion-search');
        $response->assertDontSee('Sorry, there are no discussion to display');
        $response->assertDontSee('Start a Discussion');
    }

    /**
     * @throws Exception
     */
    public function testShowDiscussionsNewest()
    {
        $response = $this->get('discussions?search[type]=recent-posts');

        $response->assertOK();
        $response->assertSeeElement('#discussion-search');
        $response->assertDontSee('Sorry, there are no discussion to display');
    }

    /**
     * @throws Exception
     */
    public function testShowDiscussionsUnanswered()
    {
        $response = $this->get('discussions?search[type]=unanswered');

        $response->assertOK();
        $response->assertSeeElement('#discussion-search');
        $response->assertDontSee('Sorry, there are no discussion to display');
    }

    /**
     * @throws Exception
     */
    public function testShowDiscussionsMy()
    {
        $response = $this->get('discussions?search[type]=my-threads');

        $response->assertOK();
        $response->assertSeeElement('#discussion-search');
        $response->assertSee('Sorry, there are no discussion to display');
    }

    /**
     * @throws Exception
     */
    public function testCanUserSeeTheCreateDiscussionButton()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this->actingAs($user)->get('discussions');

        $response->assertOK();
        $response->assertSeeElement('#discussion-search');
        $response->assertDontSee('Sorry, there are no discussion to display');
        $response->assertSee('Start a Discussion');
    }

    /**
     * @throws Exception
     */
    public function testCanUserSeeTheCreateDiscussionPage()
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
    public function testCanUserCreateDiscussion()
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
    public function testShowDiscussionsThread()
    {
        $response = $this->get('discussions/cat-1-sub-category-1/sample-thread-1');

        $response->assertOK();
        $response->assertSeeElement('#thread');
        $response->assertSeeElement('#replies-content');
    }
}
