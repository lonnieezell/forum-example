<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
use App\Entities\Thread;
use App\Models\ForumModel;
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

        $forumDropdown = model(ForumModel::class)->findAllNestedDropdown();

        if ($this->request->is('post')) {

            $validForumIds = array_reduce($forumDropdown, fn($keys, $innerArray) => array_merge($keys, array_keys($innerArray)), []);
            $validForumIds = implode(',', $validForumIds);

            if ($this->validate([
                'title' => ['required', 'string', 'max_length[255]'],
                'forum_id' => ['required', "in_list[{$validForumIds}]"],
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
            'forum_dropdown' => array_merge_recursive(['' => 'Select...'], $forumDropdown),
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
