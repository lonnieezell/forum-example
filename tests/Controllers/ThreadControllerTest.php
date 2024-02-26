<?php

namespace Tests\Controllers;

use App\Models\Factories\ImageFactory;
use App\Models\Factories\UserFactory;
use App\Models\ThreadModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\I18n\Time;
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
        $response->assertSee('Start a new Discussion');

        $response->assertSeeElement('textarea[data-upload-enabled=0]');

        // Update their trust level to 1 and check again.
        $user->trust_level = 1;
        model(UserModel::class)->save($user);

        $response = $this->actingAs($user)->get('discussions/new');
        $response->assertSeeElement('textarea[data-upload-enabled=1]');
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

        fake(ImageFactory::class, [
            'user_id' => $user->id,
            'name'    => 'test_image1.jpg',
        ]);
        fake(ImageFactory::class, [
            'user_id' => $user->id,
            'name'    => 'test_image2.jpg',
        ]);

        $fileUrl  = base_url('uploads/' . $user->id . '/test_image1.jpg');
        $response = $this
            ->withHeaders([csrf_header() => csrf_hash()])
            ->actingAs($user)->post('discussions/new', [
                'title'       => 'A new thread',
                'category_id' => 2,
                'body'        => 'Sample body ![](' . $fileUrl . ')',
            ]);

        $response->assertStatus(200);
        $response->assertHeader('hx-redirect', site_url(implode('/', [
            'discussions', 'cat-1-sub-category-1', 'a-new-thread',
        ])));

        $this->seeInDatabase('threads', ['title' => 'A new thread']);
        $this->seeInDatabase('images', ['name' => 'test_image1.jpg', 'is_used' => 1, 'thread_id' => 3]);
        $this->seeInDatabase('images', ['name' => 'test_image2.jpg', 'is_used' => 0, 'thread_id' => null]);
    }

    /**
     * @throws Exception
     */
    public function testCanUserCreateADiscussionWithTags()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this
            ->withHeaders([csrf_header() => csrf_hash()])
            ->actingAs($user)->post('discussions/new', [
                'title'       => 'A new thread',
                'category_id' => 2,
                'tags'        => 'tag1,tag2',
                'body'        => 'Sample body',
            ]);

        $response->assertStatus(200);
        $response->assertHeader('hx-redirect', site_url(implode('/', [
            'discussions', 'cat-1-sub-category-1', 'a-new-thread',
        ])));

        $this->seeInDatabase('threads', ['title' => 'A new thread']);
        $this->seeInDatabase('tags', ['name' => 'tag1']);
        $this->seeInDatabase('tags', ['name' => 'tag2']);
        $this->seeInDatabase('taggable', ['taggable_id' => 3, 'taggable_type' => 'threads', 'tag_id' => 1]);
        $this->seeInDatabase('taggable', ['taggable_id' => 3, 'taggable_type' => 'threads', 'tag_id' => 2]);
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
            'HX-Request'  => 'true',
            csrf_header() => csrf_hash(),
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
            'HX-Request'  => 'true',
            csrf_header() => csrf_hash(),
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
        $response->assertSee('Edit the thread', 'div');
    }

    /**
     * @throws Exception
     */
    public function testCanUserUpdateHisOwnThread()
    {
        $user = model(UserModel::class)->find(1);

        fake(ImageFactory::class, [
            'user_id' => $user->id,
            'name'    => 'test_image1.jpg',
        ]);
        fake(ImageFactory::class, [
            'user_id' => $user->id,
            'name'    => 'test_image2.jpg',
        ]);

        $fileUrl  = base_url('uploads/' . $user->id . '/test_image2.jpg');
        $response = $this->actingAs($user)->withBody(http_build_query([
            'title'       => 'A updated thread',
            'category_id' => 2,
            'body'        => 'Sample updated body ![](' . $fileUrl . ')',
        ]))->withHeaders([csrf_header() => csrf_hash()])->put('discussions/1/edit');

        $response->assertOK();
        $response->assertSee('A updated thread', 'h3');
        $this->seeInDatabase('threads', ['title' => 'A updated thread']);
        $this->seeInDatabase('images', ['name' => 'test_image1.jpg', 'is_used' => 0, 'thread_id' => null]);
        $this->seeInDatabase('images', ['name' => 'test_image2.jpg', 'is_used' => 1, 'thread_id' => 1]);
    }

    /**
     * @throws Exception
     */
    public function testCanUserUpdateHisOwnThreadWithTags()
    {
        $user     = model(UserModel::class)->find(1);
        $response = $this->actingAs($user)->withBody(http_build_query([
            'title'       => 'A updated thread',
            'category_id' => 2,
            'tags'        => 'tag1,tag2',
            'body'        => 'Sample updated body',
        ]))->withHeaders([csrf_header() => csrf_hash()])->put('discussions/1/edit');

        $response->assertOK();
        $response->assertSee('A updated thread', 'h3');
        $this->seeInDatabase('threads', ['title' => 'A updated thread']);
        $this->seeInDatabase('tags', ['name' => 'tag1']);
        $this->seeInDatabase('tags', ['name' => 'tag2']);
        $this->seeInDatabase('taggable', ['taggable_id' => 1, 'taggable_type' => 'threads', 'tag_id' => 1]);
        $this->seeInDatabase('taggable', ['taggable_id' => 1, 'taggable_type' => 'threads', 'tag_id' => 2]);
    }

    /**
     * @throws Exception
     */
    public function testIsUnusedTagsAreCleared()
    {
        $user     = model(UserModel::class)->find(2);
        $response = $this->actingAs($user)->withBody(http_build_query([
            'title'       => 'A updated thread',
            'category_id' => 2,
            'tags'        => 'tag1,tag2',
            'body'        => 'Sample updated body',
        ]))->withHeaders([csrf_header() => csrf_hash()])->put('discussions/2/edit');

        $response->assertOK();
        $response->assertSee('A updated thread', 'h3');
        $this->seeInDatabase('threads', ['title' => 'A updated thread']);

        $this->seeInDatabase('tags', ['name' => 'tag1']);
        $this->seeInDatabase('tags', ['name' => 'tag2']);
        $this->dontSeeInDatabase('tags', ['name' => 'tag3']);

        $this->seeInDatabase('taggable', ['taggable_id' => 2, 'taggable_type' => 'threads', 'tag_id' => 1]);
        $this->seeInDatabase('taggable', ['taggable_id' => 2, 'taggable_type' => 'threads', 'tag_id' => 2]);
        $this->dontSeeInDatabase('taggable', ['taggable_id' => 2, 'taggable_type' => 'threads', 'tag_id' => 3]);
    }

    public function testManageAnswerSet()
    {
        Time::setTestNow('January 10, 2023 21:50:00');
        $user     = model(UserModel::class)->find(1);
        $response = $this->actingAs($user)->withHeaders([
            csrf_header() => csrf_hash(),
        ])->post('thread/1/set-answer', [
            'post_id' => 2,
        ]);

        $response->assertHeader('HX-Redirect', site_url('discussions/cat-1-sub-category-1/sample-thread-1'));
        $this->seeInDatabase('threads', ['id' => 1, 'answer_post_id' => 2]);
        $this->seeInDatabase('posts', ['id' => 2, 'marked_as_answer' => '2023-01-10 21:50:00']);
    }

    public function testManageAnswerUnset()
    {
        model(ThreadModel::class)->update(1, ['answer_post_id' => 2]);
        $user     = model(UserModel::class)->find(1);
        $response = $this->actingAs($user)->withHeaders([
            csrf_header() => csrf_hash(),
        ])->post('thread/1/unset-answer', [
            'post_id' => 2,
        ]);

        $response->assertHeader('HX-Redirect', site_url('discussions/cat-1-sub-category-1/sample-thread-1'));
        $this->seeInDatabase('threads', ['id' => 1, 'answer_post_id' => null]);
        $this->seeInDatabase('posts', ['id' => 2, 'marked_as_answer' => null]);
    }

    public function testManageAnswerNoThread()
    {
        $this->expectException(PageNotFoundException::class);
        $this->expectExceptionMessage('This thread does not exist.');

        $user = model(UserModel::class)->find(1);
        $this->actingAs($user)->withHeaders([
            csrf_header() => csrf_hash(),
        ])->post('thread/999999/set-answer', [
            'post_id' => 2,
        ]);
    }

    public function testManageAnswerNoCredentials()
    {
        $user     = model(UserModel::class)->find(3);
        $response = $this->actingAs($user)->withHeaders([
            csrf_header() => csrf_hash(),
            'HX-Request'  => 'true',
        ])->post('thread/1/set-answer', [
            'post_id' => 2,
        ]);

        $response->assertRedirect();
        $response->assertHeader('HX-Location', json_encode(['path' => '/display-error']));
        $response->assertSessionHas('message', 'You are not allowed to manage an answer for this thread.');
    }

    public function testManageAnswerAlreadySet()
    {
        model(ThreadModel::class)->update(1, ['answer_post_id' => 2]);
        $user     = model(UserModel::class)->find(1);
        $response = $this->actingAs($user)->withHeaders([
            csrf_header() => csrf_hash(),
        ])->post('thread/1/set-answer', [
            'post_id' => 2,
        ]);

        $response->assertSessionHas('alerts', ['error' => [
            ['message' => 'An answer has already been selected in this thread.', 'seconds' => 5],
        ]]);
    }

    public function testManageAnswerAlreadyUnset()
    {
        $user     = model(UserModel::class)->find(1);
        $response = $this->actingAs($user)->withHeaders([
            csrf_header() => csrf_hash(),
        ])->post('thread/1/unset-answer', [
            'post_id' => 2,
        ]);

        $response->assertSessionHas('alerts', ['error' => [
            ['message' => 'This thread has no answer selected yet.', 'seconds' => 5],
        ]]);
    }

    public function testManageAnswerInvalidPostId()
    {
        $user     = model(UserModel::class)->find(1);
        $response = $this->actingAs($user)->withHeaders([
            csrf_header() => csrf_hash(),
        ])->post('thread/1/set-answer', [
            'post_id' => 999999,
        ]);

        $response->assertSessionHas('alerts', ['error' => [
            ['message' => 'This post does not belong in this thread', 'seconds' => 5],
        ]]);
    }
}
