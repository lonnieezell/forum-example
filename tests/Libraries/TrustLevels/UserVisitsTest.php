<?php

namespace Tests\Libraries\TrustLevels;

use App\Commands\UpdateVisits;
use App\Models\Factories\UserFactory;
use App\Models\UserModel;
use Tests\Support\TestCase;
use CodeIgniter\I18n\Time;
use Tests\Support\Concerns\SupportsTrustLevels;

class UserVisitsTest extends TestCase
{
    use SupportsTrustLevels;

    protected $refresh = true;

    public function testSetTrustLevels()
    {
        $user = fake(UserFactory::class, [
            'active' => true,
            'trust_level' => 0,
            'thread_count' => 5,
            'post_count' => 30,
            'last_active' => Time::now()->toDateTimeString(),
        ]);
        $command = new UpdateVisits(service('logger'), service('commands'));

        $this->userVisits($user, 5);

        $result = $command->run([]);

        $this->assertNull($result);

        // The user should have 5 visits in the database
        $this->assertEquals(5, $user->countDailyVisits());
    }
}
