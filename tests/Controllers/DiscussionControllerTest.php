<?php

namespace Tests\Controllers;

use Exception;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

class DiscussionControllerTest extends TestCase
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
    public function testShowDiscussionsThread()
    {
        $response = $this->get('discussions/cat-1-sub-category-1/sample-thread-1');

        $response->assertOK();
        $response->assertSeeElement('#thread');
        $response->assertSeeElement('#replies-content');
    }
}
