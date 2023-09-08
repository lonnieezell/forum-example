<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
use App\Entities\Post;
use App\Entities\Thread;
use App\Models\PostModel;
use App\Models\ThreadModel;

/**
 * Class Post
 *
 * @package App\Controllers\Discussions
 */
class PostController extends BaseController
{
    /**
     * Create a new post
     */
    public function create(int $threadId, ?int $postId = null)
    {
        if (! auth()->user()?->can('posts.create')) {
            dd('Unauthorized');
        }

        if ($this->request->is('post')) {

            if ($this->validate([
                'thread_id' => ['required', 'is_natural_no_zero', 'thread_exists'],
                'reply_to' => ['permit_empty', 'is_natural_no_zero', 'post_exists[]'],
                'body' => ['required', 'string', 'max_length[65000]'],
            ])) {
                $post = new Post($this->validator->getValidated());
                $thread = model(ThreadModel::class)->find($post->thread_id);

                $post->category_id = $thread->category_id;
                $post->author_id = user_id();
                $post->visible = 1;

                $postModel = model(PostModel::class);

                if ($postId = $postModel->insert($post)) {
                    return redirect()->hxRedirect($thread->link());
                }
            }
        }

        helper('form');

        $data = [
            'thread_id' => $threadId,
            'post_id' => $postId ?? '',
            'validator' => $this->validator ?? service('validation'),
        ];

        return $this->render('discussions/posts/create', $data);
    }

    /**
     * Preview a new post
     */
    public function preview()
    {
        if (! auth()->user()?->can('posts.create')) {
            dd('Unauthorized');
        }

        if (! $this->validate([
            'body' => ['required', 'string', 'max_length[65000]'],
        ])) {
            return '';
        }

        $thread = new Post($this->validator->getValidated());
        $thread->markup = 'markdown';

        return view('discussions/posts/_post_preview', ['thread' => $thread]);
    }
}
