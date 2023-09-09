<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
use App\Entities\Thread;
use App\Models\CategoryModel;
use App\Models\ThreadModel;

/**
 * Class Thread
 *
 * @package App\Controllers\Discussions
 */
class ThreadController extends BaseController
{
    /**
     * Create a new thread
     */
    public function create()
    {
        if (! auth()->user()?->can('threads.create')) {
            dd('Unauthorized');
        }

        helper('form');

        $categoryDropdown = model(CategoryModel::class)->findAllNestedDropdown();

        if ($this->request->is('post')) {

            $validCategoryIds = array_reduce($categoryDropdown, fn($keys, $innerArray) => array_merge($keys, array_keys($innerArray)), []);
            $validCategoryIds = implode(',', $validCategoryIds);

            if ($this->validate([
                'title' => ['required', 'string', 'max_length[255]'],
                'category_id' => ['required', "in_list[{$validCategoryIds}]"],
                'body' => ['required', 'string', 'max_length[65000]'],
            ])) {
                $thread = new Thread($this->validator->getValidated());
                $thread->author_id = user_id();
                $thread->visible = 1;

                $threadModel = model(ThreadModel::class);

                if ($threadId = $threadModel->insert($thread)) {
                    $thread = $threadModel->find($threadId);

                    return redirect()->hxRedirect($thread->link());
                }
            }
        }

        $data = [
            'categoryDropdown' => array_merge_recursive(['' => 'Select...'], $categoryDropdown),
            'validator' => $this->validator ?? service('validation'),
        ];

        return $this->render('discussions/threads/create', $data);
    }

    /**
     * Preview a new thread
     */
    public function preview()
    {
        if (! auth()->user()?->can('threads.create')) {
            dd('Unauthorized');
        }

        if (! $this->validate([
            'title' => ['permit_empty', 'string', 'max_length[255]'],
            'body' => ['required', 'string', 'max_length[65000]'],
        ])) {
            return '';
        }

        $thread = new Thread($this->validator->getValidated());
        $thread->markup = 'markdown';

        return view('discussions/threads/_thread_preview', ['thread' => $thread]);
    }
}
