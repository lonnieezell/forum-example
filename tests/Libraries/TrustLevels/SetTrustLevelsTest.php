<?php

namespace Tests\Libraries\TrustLevels;

use App\Commands\SetTrustLevels;
use App\Models\Factories\UserFactory;
use App\Models\UserModel;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Test\Mock\MockInputOutput;
use Tests\Support\TestCase;
use CodeIgniter\I18n\Time;

class SetTrustLevelsTest extends TestCase
{
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

        $io = new MockInputOutput();
        CLI::setInputOutput($io);

        $command = new SetTrustLevels(service('logger'), service('commands'));

        $result = $command->run([]);

        $this->assertNull($result);

        // The user should be trust level 1
        $user = model(UserModel::class)->find($user->id);

        $this->assertEquals(1, $user->trust_level);

        CLI::resetInputOutput();
    }
}
