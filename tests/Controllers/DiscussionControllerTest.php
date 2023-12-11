<?php

namespace Tests\Controllers;

use App\Models\Factories\UserFactory;
use CodeIgniter\Exceptions\PageNotFoundException;
use Exception;
use InvalidArgumentException;
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
     * @dataProvider provideShowDiscussionSearchByType
     */
    public function testShowDiscussionSearchByType(string $input, bool $see)
    {
        $response = $this->get('discussions?search[type]=' . $input);

        $response->assertOK();
        $response->assertSeeElement('#discussion-search');

        if ($see) {
            $response->assertSee('Sorry, there are no discussion to display');
        } else {
            $response->assertDontSee('Sorry, there are no discussion to display');
        }
    }

    public static function provideShowDiscussionSearchByType(): iterable
    {
        yield ['recent-threads', false];

        yield ['recent-posts', false];

        yield ['unanswered', false];

        yield ['my-threads', true];
    }

    /**
     * @throws Exception
     */
    public function testShowDiscussionSearchByTypeValidationError()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The search.type field must be one of: recent-threads,recent-posts,unanswered,my-threads.'
        );

        $this->get('discussions?search[type]=wrong-type');
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
    public function testShowCategory()
    {
        $response = $this->get('c/cat-1-sub-category-1');

        $response->assertOK();
        $response->assertSeeElement('#discussion-search');
        $response->assertDontSee('Sorry, there are no discussion to display');
        $response->assertDontSee('Start a Discussion');
    }

    /**
     * @dataProvider provideShowDiscussionSearchByType
     */
    public function testShowCategorySearchByType(string $input, bool $see)
    {
        $response = $this->get('c/cat-1-sub-category-1?search[type]=' . $input);

        $response->assertOK();
        $response->assertSeeElement('#discussion-search');

        if ($see) {
            $response->assertSee('Sorry, there are no discussion to display');
        } else {
            $response->assertDontSee('Sorry, there are no discussion to display');
        }
    }

    /**
     * @throws Exception
     */
    public function testShowCategorySearchByTypeValidationError()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The search.type field must be one of: recent-threads,recent-posts,unanswered,my-threads.'
        );

        $this->get('c/cat-1-sub-category-1?search[type]=wrong-type');
    }

    /**
     * @throws Exception
     */
    public function testShowTag()
    {
        $response = $this->get('t/tag1');

        $response->assertOK();
        $response->assertSeeElement('#discussion-search');
        $response->assertDontSee('Sorry, there are no discussion to display');
        $response->assertDontSee('Start a Discussion');
    }

    /**
     * @dataProvider provideShowDiscussionSearchByType
     */
    public function testShowTagSearchByType(string $input, bool $see)
    {
        $response = $this->get('t/tag1?search[type]=' . $input);

        $response->assertOK();
        $response->assertSeeElement('#discussion-search');

        if ($see) {
            $response->assertSee('Sorry, there are no discussion to display');
        } else {
            $response->assertDontSee('Sorry, there are no discussion to display');
        }
    }

    /**
     * @throws Exception
     */
    public function testShowTagSearchByTypeValidationError()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The search.type field must be one of: recent-threads,recent-posts,unanswered,my-threads.'
        );

        $this->get('t/tag1?search[type]=wrong-type');
    }

    /**
     * @throws Exception
     */
    public function testShowDiscussionsThread()
    {
        $response = $this->get('discussions/cat-1-sub-category-1/sample-thread-1');

        $response->assertOK();
        $response->assertSeeElement('#thread-wrap');
        $response->assertSeeElement('#replies-content');
    }

    /**
     * @throws Exception
     */
    public function testDiscussionsThread404()
    {
        $this->expectException(PageNotFoundException::class);
        $this->get('discussions/cat-1-sub-category-1/sample-thread-123456789');
    }

    /**
     * @throws Exception
     */
    public function testDiscussionsThreadSlugValidationError()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The slug field cannot exceed 255 characters in length.');

        helper('text');
        $slug = random_string('alnum', 256);
        $this->get('discussions/cat-1-sub-category-1/' . $slug);
    }

    /**
     * @throws Exception
     */
    public function testGuestDontSeeMuteNotificationsButton()
    {
        $response = $this->get('discussions/cat-1-sub-category-1/sample-thread-1');

        $response->assertOK();
        $response->assertDontSeeElement('#mute-thread-cell');
    }

    /**
     * @throws Exception
     */
    public function testUserSeeMuteNotificationsButton()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this->actingAs($user)->get('discussions/cat-1-sub-category-1/sample-thread-1');

        $response->assertOK();
        $response->assertSeeElement('#mute-thread-cell');
    }
}
