<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
use App\Entities\Thread;
use App\Models\CategoryModel;
use App\Models\ThreadModel;
use CodeIgniter\I18n\Time;
use Exception;
use ReflectionException;

/**
 * Class Thread
 */
class ThreadController extends BaseController
{
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

        helper('form');

        $categoryDropdown = model(CategoryModel::class)->findAllNestedDropdown();

        if ($this->request->is('post')) {
            $validCategoryIds = array_reduce($categoryDropdown, static fn ($keys, $innerArray) => array_merge($keys, array_keys($innerArray)), []);
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

                    return redirect()->hxRedirect($thread->link());
                }
            }
        }

        $data = [
            'categoryDropdown' => array_merge_recursive(['' => 'Select...'], $categoryDropdown),
            'validator'        => $this->validator ?? service('validation'),
        ];

        return $this->render('discussions/threads/create', $data);
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

        if (! $this->policy->can('threads.edit', $thread)) {
            return $this->policy->deny('You are not allowed to edit this thread.');
        }

        helper('form');

        $categoryDropdown = model(CategoryModel::class)->findAllNestedDropdown();

        if ($this->request->is('put')) {
            $validCategoryIds = array_reduce($categoryDropdown, static fn ($keys, $innerArray) => array_merge($keys, array_keys($innerArray)), []);
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
                    return view('discussions/threads/_thread', ['thread' => $threadModel->withUsers($thread)]);
                }
            }
        }

        $data = [
            'thread'            => $thread,
            'category_dropdown' => $categoryDropdown,
            'validator'         => $this->validator ?? service('validation'),
        ];

        return view('discussions/threads/_edit', $data);
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

        return view('discussions/threads/_thread_preview', ['thread' => $thread]);
    }
}
