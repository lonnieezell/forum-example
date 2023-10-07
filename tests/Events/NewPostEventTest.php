<?php

use App\Entities\Category;
use App\Entities\Post;
use App\Entities\Thread;
use App\Events\NewPostEvent;
use App\Models\CategoryModel;
use App\Models\Factories\PostFactory;
use App\Models\Factories\UserFactory;
use App\Models\NotificationSettingModel;
use App\Models\PostModel;
use App\Models\ThreadModel;
use CodeIgniter\Test\ReflectionHelper;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class NewPostEventTest extends TestCase
{
    use ReflectionHelper;

    protected $seed = TestDataSeeder::class;

    /**
     * @dataProvider provideHandleThreadNotifications
     * @throws ReflectionException
     */
    public function testHandleThreadNotifications(int $emailThread, bool $result, int $count)
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');

        $post = fake(PostFactory::class, [
            'category_id' => 1,
            'thread_id'   => 1,
            'author_id'   => $user->id,
        ]);
        /** @var Post $post */
        $post   = model(PostModel::class)->withUsers($post);
        $thread = model(ThreadModel::class)->find(1);
        /** @var Thread $thread */
        $thread = model(ThreadModel::class)->withUsers($thread);
        /** @var Category $category */
        $category = model(CategoryModel::class)->find(1);

        model(NotificationSettingModel::class)->save([
            'user_id'      => 1,
            'email_thread' => $emailThread,
        ]);

        $event  = new NewPostEvent($category, $thread, $post);
        $method = $this->getPrivateMethodInvoker($event, 'handleThreadNotifications');
        $this->assertSame($result, $method());
        $this->assertSame($event->getCount(), $count);
    }

    public static function provideHandleThreadNotifications(): iterable
    {
        yield [1, true, 1];

        yield [0, false, 0];
    }

    /**
     * @dataProvider provideHandlePostNotifications
     * @throws ReflectionException
     */
    public function testHandlePostNotifications(bool $addPost1, bool $replyTo, ?int $userId, int $emailPost, int $emailPostReply, bool $result, int $count)
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');

        $post1 = null;
        if ($addPost1) {
            // post 1
            /** @var Post $post1 */
            $post1 = fake(PostFactory::class, [
                'category_id' => 1,
                'thread_id'   => 1,
                'author_id'   => 1,
            ]);
        }

        // post 2
        $post2 = fake(PostFactory::class, [
            'category_id' => 1,
            'thread_id'   => 1,
            'author_id'   => $user->id,
            'reply_to'    => $replyTo ? $post1?->id : null,
        ]);
        /** @var Post $post2 */
        $post2 = model(PostModel::class)->withUsers($post2);

        $thread = model(ThreadModel::class)->find(1);
        /** @var Thread $thread */
        $thread = model(ThreadModel::class)->withUsers($thread);
        /** @var Category $category */
        $category = model(CategoryModel::class)->find(1);

        model(NotificationSettingModel::class)->save([
            'user_id'          => $userId ?? $user->id,
            'email_post'       => $emailPost,
            'email_post_reply' => $emailPostReply,
        ]);

        $event  = new NewPostEvent($category, $thread, $post2);
        $method = $this->getPrivateMethodInvoker($event, 'handlePostNotifications');
        $this->assertSame($result, $method());
        $this->assertSame($event->getCount(), $count);
    }

    public static function provideHandlePostNotifications(): iterable
    {
        yield [false, false, null, 0, 0, false, 0];

        yield [true, false, null, 0, 0, false, 0];

        yield [true, false, 1, 1, 0, true, 1];

        yield [true, false, 1, 0, 1, false, 0];

        yield [true, true, 1, 0, 1, true, 1];
    }
}
