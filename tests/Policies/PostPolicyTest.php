<?php

namespace Tests\Policies;

use App\Models\Factories\UserFactory;
use App\Policies\PostPolicy;
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
}
