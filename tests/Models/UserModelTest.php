<?php

namespace Tests\Models;

use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Database\Seeds\TestAccountDataSeeder;

/**
 * @internal
 */
final class UserModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace;
    protected $seed = TestAccountDataSeeder::class;
    private UserModel $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = model(UserModel::class);
    }

    public function testAfterUserDeleteAndRestore()
    {
        $this->model->delete(1);

        $this->seeInDatabase('users', ['id' => 1, 'thread_count' => 0, 'post_count' => 0]);
        $this->seeInDatabase('users', ['id' => 2, 'thread_count' => 1, 'post_count' => 3]);
        $this->seeInDatabase('threads', ['id' => 1, 'post_count' => 0, 'last_post_id' => null]);
        $this->seeInDatabase('threads', ['id' => 2, 'post_count' => 4, 'last_post_id' => 14]);
        $this->seeInDatabase('categories', ['id' => 2, 'thread_count' => 1, 'post_count' => 4, 'last_thread_id' => 2]);

        $user = $this->model->onlyDeleted()->find(1);
        $this->model->restore($user->id, $user->deleted_at);

        $this->seeInDatabase('users', ['id' => 1, 'thread_count' => 1, 'post_count' => 10]);
        $this->seeInDatabase('users', ['id' => 2, 'thread_count' => 1, 'post_count' => 5]);
        $this->seeInDatabase('threads', ['id' => 1, 'post_count' => 8, 'last_post_id' => 8]);
        $this->seeInDatabase('threads', ['id' => 2, 'post_count' => 7, 'last_post_id' => 15]);
        $this->seeInDatabase('categories', ['id' => 2, 'thread_count' => 2, 'post_count' => 15, 'last_thread_id' => 2]);
    }
}
