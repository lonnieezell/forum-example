<?php

namespace Tests\Policies;

use App\Models\Factories\ThreadFactory;
use App\Models\Factories\UserFactory;
use App\Policies\ThreadPolicy;
use Tests\Support\TestCase;

class ThreadPolicyTest extends TestCase
{
    protected $policy;

    public function setUp(): void
    {
        parent::setUp();

        $this->policy = new ThreadPolicy();
    }

    public function testCanEdit()
    {
        $user = fake(UserFactory::class, [
            'trust_level' => 0,
        ]);
        $user->addGroup('user');
        $thread = fake(ThreadFactory::class, [
            'author_id' => $user->id,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Trust level 0 should be able to edit their own post
        $this->assertTrue($this->policy->edit($user, $thread));

        // Trust level 0 should be able to edit their own post
        // when the post is more than 24 hours old
        $thread->created_at = date('Y-m-d H:i:s', strtotime('-23 hours'));
        $this->assertTrue($this->policy->edit($user, $thread));

        // Trust level 0 should not be able to edit their own post
        // when the thread is older than 24 hours
        $thread->created_at = date('Y-m-d H:i:s', strtotime('-25 hours'));
        $this->assertFalse($this->policy->edit($user, $thread));
    }
}
