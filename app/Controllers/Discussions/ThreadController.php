<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
use App\Entities\Thread;
use App\Managers\CategoryManager;
use App\Models\ThreadModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\I18n\Time;
use Exception;
use ReflectionException;

/**
 * Class Thread
 */
class ThreadController extends BaseController
{
    /**
     * Show thread.
     *
     * @todo Secure the access
     *
     * @throws PageNotFoundException
     */
    public function show(int $threadId)
    {
        $threadModel = model(ThreadModel::class);
        $thread      = $threadModel->withTags()->find($threadId);

        if (! $thread) {
            throw PageNotFoundException::forPageNotFound('Thread not found.');
        }

        // Check if you're allowed to see the thread based on the category permissions
        if (! $this->policy->checkCategoryPermissions($thread->category_id)) {
            return $this->policy->deny('You are not allowed to access this thread');
        }

        return $this->render('discussions/threads/_thread', [
            'thread' => $threadModel->withUsers($thread),
        ]);
    }

    /**
     * Create a new thread
     *
     * @throws ReflectionException
     */
    public function create()
    {
        if (! $this->policy->can('threads.create')) {
            return $this->policy->deny('You are not allowed to create threads.');
        }

        $categoryDropdown = manager(CategoryManager::class)->findAllNestedDropdown();

        if ($this->request->is('post')) {
            $validCategoryIds = array_reduce($categoryDropdown, static fn ($keys, $innerArray) => [...$keys, ...array_keys($innerArray)], []);
            $validCategoryIds = implode(',', $validCategoryIds);

            if ($this->validate([
                'title'       => ['required', 'string', 'max_length[255]'],
                'category_id' => ['required', "in_list[{$validCategoryIds}]"],
                'tags'        => ['permit_empty', 'string', 'valid_tags[5]'],
                'body'        => ['required', 'string', 'max_length[65000]'],
            ])) {
                $thread            = new Thread($this->validator->getValidated());
                $thread->author_id = user_id();
                $thread->visible   = 1;

                $threadModel = model(ThreadModel::class);

                if ($threadId = $threadModel->insert($thread)) {
                    $thread = $threadModel->find($threadId);

                    alerts()->set('success', 'Your new discussion has been added successfully');

                    return redirect()->hxRedirect($thread->link());
                }
                alerts()->set('error', 'Something went wrong');
            }
        }

        $data = [
            'categoryDropdown' => array_merge_recursive(['' => 'Select...'], $categoryDropdown),
            'validator'        => $this->validator ?? service('validation'),
        ];

        return $this->render('discussions/create', $data);
    }

    /**
     * Edit thread
     *
     * @throws Exception
     */
    public function edit(int $threadId)
    {
        $threadModel = model(ThreadModel::class);

        $thread = $threadModel->withTags()->find($threadId);

        if (! $thread) {
            throw PageNotFoundException::forPageNotFound('This thread does not exist.');
        }

        if (! $this->policy->can('threads.edit', $thread)) {
            return $this->policy->deny('You are not allowed to edit this thread.');
        }

        $categoryDropdown = manager(CategoryManager::class)->findAllNestedDropdown();

        if ($this->request->is('put')) {
            $validCategoryIds = array_reduce($categoryDropdown, static fn ($keys, $innerArray) => [...$keys, ...array_keys($innerArray)], []);
            $validCategoryIds = implode(',', $validCategoryIds);

            if ($this->validate([
                'title'       => ['required', 'string', 'max_length[255]'],
                'category_id' => ['required', "in_list[{$validCategoryIds}]"],
                'tags'        => ['permit_empty', 'string', 'valid_tags[5]'],
                'body'        => ['required', 'string', 'max_length[65000]'],
            ])) {
                $thread->fill($this->validator->getValidated());
                $thread->editor_id = user_id();
                $thread->edited_at = Time::now('UTC');

                if ($thread->hasChanged('category_id')) {
                    // We need to update all the stats
                    $threadModel->withStats($thread->getOriginalCategoryId());
                }

                if ($threadModel->update($threadId, $thread)) {
                    $thread = $threadModel->withTags()->find($threadId);

                    alerts()->set('success', 'Changes to the discussion has been saved successfully');

                    return $this->render('discussions/threads/_thread', ['thread' => $threadModel->withUsers($thread)]);
                }
                alerts()->set('error', 'Something went wrong');
            }
        }

        $data = [
            'thread'            => $thread,
            'category_dropdown' => $categoryDropdown,
            'validator'         => $this->validator ?? service('validation'),
        ];

        return $this->render('discussions/threads/_edit', $data);
    }

    /**
     * Preview a new thread
     */
    public function preview()
    {
        if (! $this->policy->can('threads.create')) {
            return $this->policy->deny('You are not allowed to create threads.');
        }

        if (! $this->validate([
            'title' => ['permit_empty', 'string', 'max_length[255]'],
            'body'  => ['required', 'string', 'max_length[65000]'],
        ])) {
            return '';
        }

        $thread         = new Thread($this->validator->getValidated());
        $thread->markup = 'markdown';

        $this->response->triggerClientEvent('preview-show');

        return $this->render('discussions/threads/_thread_preview', ['thread' => $thread]);
    }

    /**
     * Set / unset an answer for the thread.
     */
    public function manageAnswer(int $threadId, string $type = 'set')
    {
        $threadModel = model(ThreadModel::class);
        $thread      = $threadModel->find($threadId);

        if (! $thread) {
            throw PageNotFoundException::forPageNotFound('This thread does not exist.');
        }

        if (! $this->policy->can('threads.manageAnswer', $thread)) {
            return $this->policy->deny('You are not allowed to manage an answer for this thread.');
        }

        if ($type === 'set' && $thread->answer_post_id !== null) {
            alerts()->set('error', 'An answer has already been selected in this thread.');

            return '';
        }
        if ($type === 'unset' && $thread->answer_post_id === null) {
            alerts()->set('error', 'This thread has no answer selected yet.');

            return '';
        }

        if (! $this->validate([
            'post_id' => ['required', 'string', "valid_post_thread[{$threadId}]"],
        ])) {
            alerts()->set('error', $this->validator->getError('post_id'));

            return '';
        }

        $method = $type . 'Answer';
        $postId = $this->request->getPost('post_id');

        $threadModel->{$method}($threadId, $postId);

        return redirect()->hxRedirect($thread->link());
    }
}
