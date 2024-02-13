<?php

namespace Tests\Entities;

use App\Entities\User;
use Tests\Support\TestCase;

class UserTest extends TestCase
{
    public function testCanTrustTo()
    {
        $user = new User([
            'trust_level' => null,
        ]);

        // Should fail nicely when no trust level is set
        $this->assertFalse($user->canTrustTo('flag'));

        // Should return false with a valid action, but not at the right level
        $user->trust_level = 0;
        $this->assertFalse($user->canTrustTo('attach'));

        // Should return false with a non-action
        $user->trust_level = 1;
        $this->assertFalse($user->canTrustTo(1));

        // Should return true with a valid action
        $this->assertTrue($user->canTrustTo('flag'));
    }
}
