<?php

namespace Tests\Cells;

use App\Cells\ActionBarCell;
use App\Entities\User;
use App\Models\Factories\CategoryFactory;
use App\Models\Factories\PostFactory;
use App\Models\Factories\ThreadFactory;
use App\Models\Factories\UserFactory;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class ActionBarTest extends TestCase
{
    private ActionBarCell $cell;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = fake(UserFactory::class);
        $this->user->addGroup('user');
        $this->cell = new ActionBarCell();
    }

    public function testUserOwnThread(): void
    {
        $thread = fake(ThreadFactory::class, [
            'author_id'   => $this->user->id,
            'category_id' => fake(CategoryFactory::class)->id,
        ], true);

        $this->cell->mount($thread, $this->user);

        $this->assertSame($this->user, $this->cell->user);
        $this->assertSame($thread, $this->cell->record);
        $this->assertSame('thread', $this->cell->type);
        $this->assertTrue($this->cell->isThread());
        $this->assertFalse($this->cell->isPost());

        // Should be able to edit and delete own thread.
        $this->assertTrue($this->cell->canEdit());
        $this->assertTrue($this->cell->canDelete());

        // Should be able to reply to own thread.
        $this->assertTrue($this->cell->canReply());

        // Cannot manage answers on your own thread - only on the posts in the thread.
        $this->assertFalse($this->cell->canManageAnswer());

        // Should not be able to report own thread.
        $this->assertFalse($this->cell->canReport());
    }

    public function testUserOtherThread(): void
    {
        $thread = fake(ThreadFactory::class, [
            'author_id'   => fake(UserFactory::class)->id,
            'category_id' => fake(CategoryFactory::class)->id,
        ], true);

        $this->cell->mount($thread, $this->user);

        // User should not be able to edit or delete other thread.
        $this->assertFalse($this->cell->canEdit());
        $this->assertFalse($this->cell->canDelete());

        // User should be able to reply to other thread.
        $this->assertTrue($this->cell->canReply());

        // User should be able to manage the answer to a thread.
        $this->assertFalse($this->cell->canManageAnswer());

        // Can report someone else's thread.
        $this->assertTrue($this->cell->canReport());
    }

    public function testUserOwnPost(): void
    {
        $category = fake(CategoryFactory::class);
        $myThread = fake(ThreadFactory::class, [
            'author_id'   => $this->user->id,
            'category_id' => $category->id,
        ], true);
        $otherThread = fake(ThreadFactory::class, [
            'author_id'   => fake(UserFactory::class)->id,
            'category_id' => $category->id,
        ], true);
        $post = fake(PostFactory::class, [
            'author_id'   => $this->user->id,
            'thread_id'   => $myThread->id,
            'category_id' => $category->id,
        ], true);

        $this->cell->mount($post, $this->user);

        $this->assertSame($this->user, $this->cell->user);
        $this->assertSame($post, $this->cell->record);
        $this->assertSame('post', $this->cell->type);
        $this->assertFalse($this->cell->isThread());
        $this->assertTrue($this->cell->isPost());

        // Should be able to edit and delete own post.
        $this->assertTrue($this->cell->canEdit());
        $this->assertTrue($this->cell->canDelete());

        // Should be able to reply to own post.
        $this->assertTrue($this->cell->canReply());

        // Should not be able to report own post.
        $this->assertFalse($this->cell->canReport());

        // Since it's your thread, you should be able to manage the answer.
        $this->assertTrue($this->cell->canManageAnswer());

        // Should not be able to manage the answer on someone else's thread.
        $post->thread_id = $otherThread->id;
        $this->cell->mount($post, $this->user);
        $this->assertFalse($this->cell->canManageAnswer());
    }

    public function testUserOtherPost(): void
    {
        $category = fake(CategoryFactory::class);
        $myThread = fake(ThreadFactory::class, [
            'author_id'   => $this->user->id,
            'category_id' => $category->id,
        ], true);
        $otherThread = fake(ThreadFactory::class, [
            'author_id'   => fake(UserFactory::class)->id,
            'category_id' => $category->id,
        ], true);
        $post = fake(PostFactory::class, [
            'author_id'   => fake(UserFactory::class)->id,
            'thread_id'   => $myThread->id,
            'category_id' => $category->id,
        ], true);

        $this->cell->mount($post, $this->user);

        // Should not be able to edit or delete other post.
        $this->assertFalse($this->cell->canEdit());
        $this->assertFalse($this->cell->canDelete());

        // Should be able to reply to other post.
        $this->assertTrue($this->cell->canReply());

        // Should be able to report other post.
        $this->assertTrue($this->cell->canReport());

        // Since it's your thread, you should be able to manage the answer.
        $this->assertTrue($this->cell->canManageAnswer());

        // Should not be able to manage the answer on someone else's thread.
        $post->thread_id = $otherThread->id;
        $this->cell->mount($post, $this->user);
        $this->assertFalse($this->cell->canManageAnswer());
    }
}
