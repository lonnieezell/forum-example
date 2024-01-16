<?php

namespace Tests\Controllers;

use App\Entities\User;
use App\Models\Factories\PostFactory;
use App\Models\Factories\ThreadFactory;
use App\Models\Factories\UserFactory;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class ReactionControllerTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = fake(UserFactory::class);
    }

    public function testMustBeLoggedInToReact()
    {
        $thread = fake(ThreadFactory::class, ['author_id' => $this->user->id]);

        $response = $this->withHeaders([csrf_header() => csrf_hash()])
            ->post("content/react/{$thread->id}/thread");

        $response->assertRedirectTo(url_to('login'));
    }

    public function testReactToThread()
    {
        $thread = fake(ThreadFactory::class, ['author_id' => $this->user->id]);

        // Like the thread
        $response = $this->actingAs($this->user)
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post("content/react/{$thread->id}/thread");

        $response->assertOk();

        // Check the database
        $this->seeInDatabase('reactions', [
            'reactor_id' => $this->user->id,
            'thread_id'  => $thread->id,
            'reaction'   => 1,
        ]);

        // Both the thread author and thread should have incremented reaction_count values
        $this->seeInDatabase('threads', [
            'id'             => $thread->id,
            'reaction_count' => 1,
        ]);
        $this->seeInDatabase('users', [
            'id'             => $thread->author_id,
            'reaction_count' => 1,
        ]);

        // Unlike the thread
        $response = $this->actingAs($this->user)
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post("content/react/{$thread->id}/thread");

        $response->assertOk();

        // Check the database
        $this->dontSeeInDatabase('reactions', [
            'reactor_id' => $this->user->id,
            'thread_id'  => $thread->id,
            'reaction'   => 1,
        ]);

        // Both the thread author and thread should have decremented reaction_count values
        $this->seeInDatabase('threads', [
            'id'             => $thread->id,
            'reaction_count' => 0,
        ]);
        $this->seeInDatabase('users', [
            'id'             => $thread->author_id,
            'reaction_count' => 0,
        ]);
    }

    public function testReactToPost()
    {
        $thread = fake(ThreadFactory::class, ['author_id' => $this->user->id]);
        $post   = fake(PostFactory::class, ['author_id' => $this->user->id, 'thread_id' => $thread->id]);

        // Like the post
        $response = $this->actingAs($this->user)
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post("content/react/{$post->id}/post");

        $response->assertOk();

        // Check the database
        $this->seeInDatabase('reactions', [
            'reactor_id' => $this->user->id,
            'post_id'    => $post->id,
            'reaction'   => 1,
        ]);

        // Both the post author and post should have incremented reaction_count values
        $this->seeInDatabase('posts', [
            'id'             => $post->id,
            'reaction_count' => 1,
        ]);
        $this->seeInDatabase('users', [
            'id'             => $post->author_id,
            'reaction_count' => 1,
        ]);

        // Unlike the post
        $response = $this->actingAs($this->user)
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post("content/react/{$post->id}/post");

        $response->assertOk();

        // Check the database
        $this->dontSeeInDatabase('reactions', [
            'reactor_id' => $this->user->id,
            'post_id'    => $post->id,
            'reaction'   => 1,
        ]);

        // Both the post author and post should have decremented reaction_count values
        $this->seeInDatabase('posts', [
            'id'             => $post->id,
            'reaction_count' => 0,
        ]);
        $this->seeInDatabase('users', [
            'id'             => $post->author_id,
            'reaction_count' => 0,
        ]);
    }
}
