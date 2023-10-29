<?php

namespace Tests\Support\Database\Seeds;

use App\Models\Factories\CategoryFactory;
use App\Models\Factories\PostFactory;
use App\Models\Factories\ThreadFactory;
use App\Models\Factories\UserFactory;
use CodeIgniter\Database\Seeder;

class TestAccountDataSeeder extends Seeder
{
    public function run(): void
    {
        helper('test');

        $category1 = fake(CategoryFactory::class, [
            'parent_id' => null, 'title' => 'Category 1',
        ]);

        $cat1SubCategory1 = fake(CategoryFactory::class, [
            'parent_id' => $category1->id, 'title' => 'Cat 1 Sub category 1',
        ]);
        // $cat1SubCategory2
        fake(CategoryFactory::class, [
            'parent_id' => $category1->id, 'title' => 'Cat 1 Sub category 2',
        ]);

        $category2 = fake(CategoryFactory::class, [
            'parent_id' => null, 'title' => 'Category 2',
        ]);
        // $cat2SubCategory1
        fake(CategoryFactory::class, [
            'parent_id' => $category2->id, 'title' => 'Cat 2 Sub category 1',
        ]);

        $user1 = fake(UserFactory::class, [
            'username' => 'testuser1',
        ]);
        $user1->addGroup('user');

        $user2 = fake(UserFactory::class, [
            'username' => 'testuser2',
        ]);
        $user2->addGroup('user')->addGroup('admin');

        $user3 = fake(UserFactory::class, [
            'username' => 'testuser3',
        ]);
        $user3->addGroup('user');

        $thread1 = fake(ThreadFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'author_id'   => $user1->id,
            'title'       => 'Sample thread 1',
            'views'       => 0,
        ]);

        // post 1
        $post1 = fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread1->id,
            'author_id'   => $user1->id,
        ]);
        // $post1Reply1
        fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread1->id,
            'reply_to'    => $post1->id,
            'author_id'   => $user2->id,
        ]);
        // $post1Reply2
        fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread1->id,
            'reply_to'    => $post1->id,
            'author_id'   => $user1->id,
        ]);
        // $post1Reply3
        fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread1->id,
            'reply_to'    => $post1->id,
            'author_id'   => $user2->id,
        ]);
        // $post1Reply4
        fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread1->id,
            'reply_to'    => $post1->id,
            'author_id'   => $user1->id,
        ]);
        $post2 = fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread1->id,
            'author_id'   => $user1->id,
        ]);
        // $post2Reply1
        fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread1->id,
            'reply_to'    => $post2->id,
            'author_id'   => $user1->id,
        ]);
        // $post3
        fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread1->id,
            'author_id'   => $user1->id,
        ]);
        // thread2
        $thread2 = fake(ThreadFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'author_id'   => $user2->id,
            'title'       => 'Sample thread 2',
        ]);
        // post4
        $post4 = fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread2->id,
            'author_id'   => $user1->id,
        ]);
        // $post4Reply1
        fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread2->id,
            'reply_to'    => $post4->id,
            'author_id'   => $user2->id,
        ]);
        // $post4Reply2
        fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread2->id,
            'reply_to'    => $post4->id,
            'author_id'   => $user1->id,
        ]);
        // $post4Reply3
        fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread2->id,
            'reply_to'    => $post4->id,
            'author_id'   => $user2->id,
        ]);
        // post5
        fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread2->id,
            'author_id'   => $user1->id,
        ]);
        // post6
        $post6 = fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread2->id,
            'author_id'   => $user2->id,
        ]);
        // $post6Reply1
        fake(PostFactory::class, [
            'category_id' => $cat1SubCategory1->id,
            'thread_id'   => $thread2->id,
            'reply_to'    => $post6->id,
            'author_id'   => $user1->id,
        ]);
    }
}
