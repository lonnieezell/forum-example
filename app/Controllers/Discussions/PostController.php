<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
use App\Entities\Post;
use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\ThreadModel;
use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\I18n\Time;
use Exception;

/**
 * Class Post
 */
class PostController extends BaseController
{
    /**
     * Show post.
     *
     * @todo Secure the access
     *
     * @throws PageNotFoundException
     */
    public function show(int $postId)
    {
        $postModel = model(PostModel::class);
        $post      = $postModel->find($postId);

        if (! $post) {
            throw PageNotFoundException::forPageNotFound();
        }

        helper('form');

        $thread = model(ThreadModel::class)->find($post->thread_id);

        return $this->render('discussions/posts/_post', ['post' => $postModel->withUsers($post), 'thread' => $thread]);
    }

    /**
     * Create a new post
     */
    public function create(int $threadId, ?int $postId = null)
    {
        if (! $this->policy->can('posts.create')) {
            return $this->policy->deny('You are not allowed to create posts.');
        }

        if ($this->request->is('post') && $this->validate([
            'thread_id' => ['required', 'is_natural_no_zero', 'thread_exists'],
            'reply_to'  => ['permit_empty', 'is_natural_no_zero', 'post_exists[]'],
            'body'      => ['required', 'string', 'max_length[65000]'],
        ])) {
            $post              = new Post($this->validator->getValidated());
            $threadModel       = model(ThreadModel::class);
            $thread            = $threadModel->find($post->thread_id);
            $post->category_id = $thread->category_id;
            $post->author_id   = user_id();
            $post->visible     = 1;
            $post->ip_address  = $this->request->getIPAddress();

            $postModel = model(PostModel::class);

            if ($postId = $postModel->insert($post)) {
                $post = $postModel->find($postId);
                $post = $postModel->withUsers($post);
                // Load category to prevent unnecessary DB call when generating a link to post.
                $category = model(CategoryModel::class)->find($post->category_id);

                Events::trigger('new-post', $category, $threadModel->withUsers($thread), $post);

                $this->response->triggerClientEvent('removePostForm', [
                    'id' => $post->reply_to === null ? 'post-reply' : 'post-reply-' . $post->reply_to,
                ]);

                alerts()->set('success', 'Your post has been added successfully');

                $thread = model(ThreadModel::class)->find($post->thread_id);

                return $this->render('discussions/posts/_post_with_replies', ['post' => $post, 'thread' => $thread]);
            }

            alerts()->set('error', 'Something went wrong');
        }

        helper('form');

        $data = [
            'thread_id' => $threadId,
            'post_id'   => $postId ?? '',
            'validator' => $this->validator ?? service('validation'),
        ];

        return $this->render('discussions/posts/_create', $data);
    }

    /**
     * Edit post
     *
     * @throws Exception
     */
    public function edit(int $postId)
    {
        helper('form');
        $postModel = model(PostModel::class);

        $post = $postModel->find($postId);

        if (! $this->policy->can('posts.edit', $post)) {
            return $this->policy->deny('You are not allowed to edit this post.');
        }

        if ($this->request->is('put') && $this->validate([
            'body' => ['required', 'string', 'max_length[65000]'],
        ])) {
            $post->fill($this->validator->getValidated());
            $post->editor_id = user_id();
            $post->edited_at = Time::now('UTC');

            if ($postModel->update($postId, $post)) {
                alerts()->set('success', 'Your post has been updated successfully');

                $thread = model(ThreadModel::class)->find($post->thread_id);

                return $this->render('discussions/posts/_post', ['post' => $postModel->withUsers($post), 'thread' => $thread]);
            }

            alerts()->set('error', 'Something went wrong');
        }

        helper('form');

        $data = [
            'post'      => $post,
            'validator' => $this->validator ?? service('validation'),
        ];

        return $this->render('discussions/posts/_edit', $data);
    }

    /**
     * Preview a new post
     */
    public function preview(): string
    {
        if (! $this->policy->can('posts.create')) {
            return $this->policy->deny('You are not allowed to create posts.');
        }

        if (! $this->validate([
            'body' => ['required', 'string', 'max_length[65000]'],
        ])) {
            return '';
        }

        $post         = new Post($this->validator->getValidated());
        $post->markup = 'markdown';

        $this->response->triggerClientEvent('preview-show');

        return $this->render('discussions/posts/_post_preview', ['post' => $post]);
    }

    /**
     * Display all replies for given post.
     *
     * @todo Secure the access
     */
    public function allReplies(int $postId): string
    {
        $posts = model(PostModel::class)->getAllReplies($postId);

        return $this->render('discussions/_thread_items', ['posts' => $posts, 'loadedReplies' => []]);
    }
}
