<?php

namespace App\Concerns;

use App\Models\CategoryModel;
use App\Models\ThreadModel;
use App\Models\UserModel;
use ReflectionException;

trait ImpactsCategoryCounts
{
    /**
     * Updates the category's discussion count.
     *
     * @throws ReflectionException
     */
    protected function incrementThreadCount(array $data): bool|array
    {
        if (! $data['result']) {
            return false;
        }

        $thread        = $this->find($data['id']);
        $categoryModel = model(CategoryModel::class);
        $userModel     = model(UserModel::class);

        // Increment Category thread count
        $category = $categoryModel->find($thread->category_id);
        $category->thread_count++;

        $categoryModel->save($category);

        // Increment User thread count
        $user = $userModel->find($thread->author_id);
        $user->thread_count++;

        $userModel->save($user);

        return $data;
    }

    /**
     * Updates the category's discussion count.
     */
    protected function decrementThreadCount(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        $thread = $this->find($data['id']);

        $categoryModel = model(CategoryModel::class);
        $userModel     = model(UserModel::class);

        // Decrement category thread count
        $category = $categoryModel->find($thread->category_id);
        $category->thread_count--;

        $categoryModel->save($category);

        // Decrement User thread count
        $user = $userModel->find($thread->author_id);
        $user->thread_count--;

        $userModel->save($user);

        return $data;
    }

    /**
     * Updates the category's post count.
     */
    protected function incrementPostCount(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        $post          = $this->find($data['id']);
        $categoryModel = model(CategoryModel::class);
        $threadModel   = model(ThreadModel::class);
        $userModel     = model(UserModel::class);

        // Increment Category post count
        $category = $categoryModel->find($post->category_id);
        $category->post_count++;

        $categoryModel->save($category);

        // Increment Thread post count
        $thread = $threadModel->find($post->thread_id);
        $thread->post_count++;

        $threadModel->save($thread);

        // Increment User post count
        $user = $userModel->find($post->author_id);
        $user->post_count++;

        $userModel->save($user);

        return $data;
    }

    /**
     * Updates the category's post count.
     */
    protected function decrementPostCount(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        $post          = $this->find($data['id']);
        $categoryModel = model(CategoryModel::class);
        $threadModel   = model(ThreadModel::class);
        $userModel     = model(UserModel::class);

        // Decrement category post count
        $category = $categoryModel->find($post->category_id);
        $category->post_count--;

        $categoryModel->save($category);

        // Decrement Thread post count
        $thread = $threadModel->find($post->thread_id);
        $thread->post_count--;

        $threadModel->save($thread);

        // Decrement User post count
        $user = $userModel->find($post->author_id);
        $user->post_count--;

        $userModel->save($user);

        return $data;
    }
}
