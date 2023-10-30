<?php

use App\Entities\User;
use App\Events\AccountDeletedEvent;
use App\Models\UserDeleteModel;
use App\Models\UserModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\I18n\Time;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Database\Seeds\TestDataSeeder;

/**
 * @internal
 */
final class AccountDeleteEventTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace;
    protected $seed = TestDataSeeder::class;

    public function testNewEventInstance()
    {
        /** @var User $user */
        $user  = model(UserModel::class)->find(1);
        $event = new AccountDeletedEvent($user);

        $this->assertInstanceOf(AccountDeletedEvent::class, $event);
        $this->seeInDatabase('users_delete', ['user_id' => $user->id]);
        $this->dontSeeInDatabase('queue_jobs', ['queue' => 'emails']);
    }

    /**
     * @throws ReflectionException
     */
    public function testNewEventInstanceError()
    {
        $this->expectException(DatabaseException::class);
        $this->expectExceptionMessage(
            'UNIQUE constraint failed: users_delete.user_id'
        );

        model(UserDeleteModel::class)->insert([
            'user_id' => 1, 'scheduled_at' => Time::now(),
        ]);

        /** @var User $user */
        $user = model(UserModel::class)->find(1);
        new AccountDeletedEvent($user);
    }

    public function testProcess()
    {
        /** @var User $user */
        $user   = model(UserModel::class)->find(1);
        $event  = new AccountDeletedEvent($user);
        $result = $event->process();

        $this->assertTrue($result);
        $this->seeInDatabase('users_delete', ['user_id' => $user->id]);
        $this->seeInDatabase('queue_jobs', ['queue' => 'emails']);
    }
}
