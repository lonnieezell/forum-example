<?php

namespace Tests\Libraries\TrustLevels;

use App\Libraries\TrustLevels\Assigner;
use App\Models\Factories\UserFactory;
use Tests\Support\Concerns\SupportsTrustLevels;
use Tests\Support\TestCase;

class AssignerTest extends TestCase
{
    use SupportsTrustLevels;

    protected $refresh = true;

    public function testAssignNewUser()
    {
        $assigner = new Assigner();
        $user = fake(UserFactory::class, ['trust_level' => 0]);

        $assigner->assignTrustLevels($user);

        // The user should still be trust level 0
        $this->assertEquals(0, $user->trust_level);
    }

    public function testAssignRaiseToLevelOne()
    {
        $assigner = new Assigner();
        $user = fake(UserFactory::class, [
            'trust_level' => 0,
            'thread_count' => 5,
            'post_count' => 30,
        ]);

        $assigner->assignTrustLevels($user);

        $this->assertEquals(1, $user->trust_level);
    }

    public function testAssignRaiseToLevelTwo()
    {
        $assigner = new Assigner();
        $user = fake(UserFactory::class, [
            'trust_level' => 0,
            'thread_count' => 20,
            'post_count' => 3,
        ]);

        $this->userLikesPosts($user, 1);
        $this->userLikedByOthers($user, 1);
        $this->userVisits($user, 15);

        $assigner->assignTrustLevels($user);

        $this->assertEquals(2, $user->trust_level);
    }

    public function testAssignDemotesLevel()
    {
        $assigner = new Assigner();
        $user = fake(UserFactory::class, [
            'trust_level' => 1,
            'thread_count' => 4,
            'post_count' => 30,
        ]);

        $assigner->assignTrustLevels($user);

        $this->assertEquals(0, $user->trust_level);
    }

    public function testAssignCannotDemotePastZero()
    {
        $assigner = new Assigner();
        $user = fake(UserFactory::class, [
            'trust_level' => 0,
            'thread_count' => 5,
            'post_count' => 30,
        ]);

        $assigner->assignTrustLevels($user);

        $this->assertEquals(1, $user->trust_level);
    }
}
