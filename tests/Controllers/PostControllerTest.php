<?php

namespace Tests\Controllers;

use App\Models\Factories\ImageFactory;
use App\Models\Factories\PostFactory;
use App\Models\Factories\ThreadFactory;
use App\Models\Factories\UserFactory;
use App\Models\UserModel;
use CodeIgniter\Cache\CacheFactory;
use Exception;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class PostControllerTest extends TestCase
{
    protected $seed = TestDataSeeder::class;

    /**
     * @throws Exception
     */
    public function testCanUserSeeTheCreateAPostPage()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this->actingAs($user)->get('posts/1');

        $response->assertOK();
        $response->assertSeeElement('.post-create');
        $response->assertSee('Create a new post', 'div');

        // Trust Level 0 should not have the upload feature.
        $response->assertSeeElement('textarea[data-upload-enabled=0]');

        // Update their trust level to 1 and check again.
        $user->trust_level = 1;
        model(UserModel::class)->save($user);

        $response = $this->actingAs($user)->get('posts/1');
        $response->assertSeeElement('textarea[data-upload-enabled=1]');
    }

    /**
     * @throws Exception
     */
    public function testCanUserCreateAPost()
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
            ->actingAs($user)->post('posts/1', [
                'thread_id' => '1',
                'reply_to'  => '',
                'body'      => 'Sample body for post ![](' . $fileUrl . ')',
            ]);

        $response->assertStatus(200);
        $response->seeElement('.post-meta');
        $response->assertHeader('hx-trigger', '{"removePostForm":{"id":"post-reply"}}');

        $this->seeInDatabase('posts', ['body' => 'Sample body for post ![](' . $fileUrl . ')']);
        $this->seeInDatabase('images', ['name' => 'test_image1.jpg', 'is_used' => 1, 'thread_id' => 1]);
        $this->seeInDatabase('images', ['name' => 'test_image2.jpg', 'is_used' => 0, 'thread_id' => null]);
    }

    /**
     * @throws Exception
     */
    public function testCanGuestSeeCreateAPostPage()
    {
        $response = $this->withHeaders([
            'HX-Request' => 'true',
        ])->get('posts/1');

        $response->assertHeader('HX-Location', '{"path":"\/display-error"}');
        $response->assertSessionHas('message', 'You are not allowed to create posts.');
        $response->assertSessionHas('status', '403');
    }

    /**
     * @throws Exception
     */
    public function testCanGuestCreateAPost()
    {
        $response = $this->withHeaders([
            'HX-Request'  => 'true',
            csrf_header() => csrf_hash(),
        ])->post('posts/1', [
            'thread_id' => '1',
            'reply_to'  => '',
            'body'      => 'Sample body',
        ]);

        $response->assertHeader('HX-Location', '{"path":"\/display-error"}');
        $response->assertSessionHas('message', 'You are not allowed to create posts.');
        $response->assertSessionHas('status', '403');
    }

    /**
     * @throws Exception
     */
    public function testCanUserSeeEditPageOfSomeoneElsePost()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this->actingAs($user)->withHeaders([
            'HX-Request' => 'true',
        ])->get('posts/1/edit');

        $response->assertHeader('HX-Location', '{"path":"\/display-error"}');
        $response->assertSessionHas('message', 'You are not allowed to edit this post.');
        $response->assertSessionHas('status', '403');
    }

    /**
     * @throws Exception
     */
    public function testCanUserUpdateSomeoneElsePost()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this->actingAs($user)->withHeaders([
            'HX-Request'  => 'true',
            csrf_header() => csrf_hash(),
        ])->withBody(http_build_query([
            'thread_id' => '1',
            'reply_to'  => '',
            'body'      => 'Sample updated post body',
        ]))->put('posts/1/edit');

        $response->assertHeader('HX-Location', '{"path":"\/display-error"}');
        $response->assertSessionHas('message', 'You are not allowed to edit this post.');
        $response->assertSessionHas('status', '403');
    }

    /**
     * @throws Exception
     */
    public function testCanUserSeeEditPageOfHisOwnPost()
    {
        $user     = model(UserModel::class)->find(1);
        $response = $this->actingAs($user)->get('posts/1/edit');

        $response->assertOK();
        $response->assertSee('Edit a post', 'div');
    }

    /**
     * @throws Exception
     */
    public function testCanUserUpdateHisOwnPost()
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
            'thread_id' => '1',
            'reply_to'  => '',
            'body'      => 'Sample updated post body ![](' . $fileUrl . ')',
        ]))->withHeaders([csrf_header() => csrf_hash()])->put('posts/1/edit');

        $response->assertOK();
        $response->assertSeeElement('.post-meta');
        $this->seeInDatabase('posts', ['body' => 'Sample updated post body ![](' . $fileUrl . ')']);
        $this->seeInDatabase('images', ['name' => 'test_image1.jpg', 'is_used' => 0, 'thread_id' => null]);
        $this->seeInDatabase('images', ['name' => 'test_image2.jpg', 'is_used' => 1, 'thread_id' => 1]);
    }

    public function testViewThreadWithSignatureNoTrust()
    {
        $mockCache = mock(CacheFactory::class);

        $author = fake(UserFactory::class, [
            'trust_level' => 0,
            'signature' => 'This signature has a [link](https://example.com)',
        ]);

        $thread = fake(ThreadFactory::class);
        $post = fake(PostFactory::class, [
            'body' => 'This is a test thread.',
            'author_id' => $author->id,
            'thread_id' => $thread->id,
        ]);

        $response = $this->get($thread->link());

        // Trust level 0 - should NOT see the link.
        $response->assertSee('This signature has a link');
    }

    public function testViewThreadWithSignatureWithTrust()
    {
        $mockCache = mock(CacheFactory::class);

        $author = fake(UserFactory::class, [
            'trust_level' => 1,
            'signature' => 'This signature has a [link](https://example.com)',
        ]);

        $thread = fake(ThreadFactory::class);
        $post = fake(PostFactory::class, [
            'body' => 'This is a test thread.',
            'author_id' => $author->id,
            'thread_id' => $thread->id,
        ]);

        $response = $this->get($thread->link());

        $response->assertSee('This signature has a <a href="https://example.com" rel="nofollow" target="_blank">link</a>');
    }
}
