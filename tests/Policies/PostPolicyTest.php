<?php

namespace Tests\Policies;

use App\Models\Factories\PostFactory;
use App\Models\Factories\ThreadFactory;
use App\Models\Factories\UserFactory;
use App\Policies\PostPolicy;
use CodeIgniter\I18n\Time;
use Config\TrustLevels;
use Tests\Support\TestCase;

class PostPolicyTest extends TestCase
{
    protected $policy;

    public function setUp(): void
    {
        parent::setUp();

        $this->policy = new PostPolicy();
    }

    public function testCanCreate()
    {
        $user = fake(UserFactory::class, [
            'trust_level' => 0,
            'post_count' => 0,
        ]);
        $user->addGroup('user');

        // Trust level 0 should be able to create a post
        // when they don't have any posts
        $this->assertTrue($this->policy->create($user));

        // Trust Level 0 should be able to create a post,
        // since they're below the threshold.
        $this->assertTrue($this->policy->create($user));

        // Trust Level 0 should not be able to create a post,
        // when they're above the threshold.
        $user->post_count = TrustLevels::POST_THRESHOLD;
        $this->assertFalse($this->policy->create($user));
    }

    public function testCanEdit()
    {
        $user = fake(UserFactory::class, [
            'trust_level' => 0,
        ]);
        $user->addGroup('user');
        $thread = fake(ThreadFactory::class);

        $post = fake(PostFactory::class, [
            'author_id' => $user->id,
            'created_at' => Time::now(),
            'thread_id' => $thread->id,
        ]);

        // Trust level 0 should be able to edit their own post
        // when the post is less than 24 hours old
        $this->assertTrue($this->policy->edit($user, $post));

        // Trust level 0 should be able to edit their own post
        // when the post is less than 24 hours old
        $post->created_at = Time::now()->subHours('23')->toDateTimeString();
        $this->assertTrue($this->policy->edit($user, $post));

        // Trust level 0 should not be able to edit their own post
        // when the post is more than 24 hours old
        $post->created_at = Time::now()->subHours('25')->toDateTimeString();
        $this->assertFalse($this->policy->edit($user, $post));
    }
}
