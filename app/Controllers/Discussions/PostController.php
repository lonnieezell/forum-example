<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
use App\Entities\Post;
use App\Models\PostModel;
use App\Models\ThreadModel;
use CodeIgniter\I18n\Time;
use Exception;

/**
 * Class Post
 */
class PostController extends BaseController
{
    /**
     * Create a new post
     */
    public function create(int $threadId, ?int $postId = null): string
    {
        if (! auth()->user()?->can('posts.create')) {
            dd('Unauthorized');
        }

        if ($this->request->is('post') && $this->validate([
            'thread_id' => ['required', 'is_natural_no_zero', 'thread_exists'],
            'reply_to'  => ['permit_empty', 'is_natural_no_zero', 'post_exists[]'],
            'body'      => ['required', 'string', 'max_length[65000]'],
        ])) {
            $post              = new Post($this->validator->getValidated());
            $thread            = model(ThreadModel::class)->find($post->thread_id);
            $post->category_id = $thread->category_id;
            $post->author_id   = user_id();
            $post->visible     = 1;
            $post->ip_address  = $this->request->getIPAddress();

            $postModel = model(PostModel::class);

            if ($postId = $postModel->insert($post)) {
                $post = $postModel->find($postId);

                return view('discussions/posts/_post', ['post' => $postModel->withUsers($post)])
                    . '<div id="post-reply" hx-swap-oob="true"></div>';
            }
        }

        helper('form');

        $data = [
            'thread_id' => $threadId,
            'post_id'   => $postId ?? '',
            'validator' => $this->validator ?? service('validation'),
        ];

        return view('discussions/posts/_create', $data);
    }

    /**
     * Edit post
     *
     * @throws Exception
     */
    public function edit(int $postId): string
    {
        $postModel = model(PostModel::class);

        $post = $postModel->find($postId);

        if (empty($post)
            || ($post->author_id !== user_id()
                && ! auth()->user()?->can('posts.edit'))) {
            dd('Unauthorized');
        }

        if ($this->request->is('put') && $this->validate([
            'body' => ['required', 'string', 'max_length[65000]'],
        ])) {
            $post->fill($this->validator->getValidated());
            $post->editor_id = user_id();
            $post->edited_at = Time::now('UTC');

            if ($postModel->update($postId, $post)) {
                return view('discussions/posts/_post', ['post' => $postModel->withUsers($post)]);
            }
        }

        helper('form');

        $data = [
            'post'      => $post,
            'validator' => $this->validator ?? service('validation'),
        ];

        return view('discussions/posts/_edit', $data);
    }

    /**
     * Preview a new post
     */
    public function preview(): string
    {
        if (! auth()->user()?->can('posts.create')) {
            dd('Unauthorized');
        }

        if (! $this->validate([
            'body' => ['required', 'string', 'max_length[65000]'],
        ])) {
            return '';
        }

        $thread         = new Post($this->validator->getValidated());
        $thread->markup = 'markdown';

        return view('discussions/posts/_post_preview', ['thread' => $thread]);
    }
}
