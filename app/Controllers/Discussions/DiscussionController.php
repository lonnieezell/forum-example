<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\ThreadModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use InvalidArgumentException;

/**
 * Class Home
 */
class DiscussionController extends BaseController
{
    protected array $types = [
        'recent-threads' => 'by Newest Threads',
        'recent-posts'   => 'by Newest Replies',
        'unanswered'     => 'only Unanswered',
        'my-threads'     => 'only My Threads',
    ];

    /**
     * Display a standard forum-style list of discussions.
     */
    public function list(): string
    {
        $table = [
            'perPage' => $this->request->getGet('perPage') ?? 20,
            'search'  => $this->request->getGet('search') ?? [],
        ];

        $rules = [
            'perPage'     => ['in_list[20]'],
            'search.type' => ['permit_empty', 'in_list[' . implode(',', array_keys($this->types)) . ']'],
        ];

        if (! $this->validateData($table, $rules)) {
            throw new InvalidArgumentException(implode(PHP_EOL, $this->validator->getErrors()));
        }

        helper('form');

        $threadModel = model(ThreadModel::class);

        $data = [
            'threads' => $threadModel->withUsers($threadModel->withTags()->forList($table['search'], $table['perPage'])),
            'table'   => [
                'dropdowns' => [
                    'type' => $this->types,
                ],
                'pager' => $threadModel->pager,
            ],
            'searchUrl' => 'discussions',
        ];

        $data['table'] = [...$table, ...$data['table']];

        return $this->render('discussions/list', $data);
    }

    /**
     * Display a standard forum-style list of discussions.
     */
    public function category(string $slug): string
    {
        $table = [
            'perPage' => $this->request->getGet('perPage') ?? 20,
            'search'  => $this->request->getGet('search') ?? [],
        ];
        $table['search']['category'] = $slug;

        $rules = [
            'perPage'         => ['in_list[20]'],
            'search.type'     => ['permit_empty', 'in_list[' . implode(',', array_keys($this->types)) . ']'],
            'search.category' => ['required', 'max_length[255]', 'category_exists[child]'],
        ];

        if (! $this->validateData($table, $rules)) {
            throw new InvalidArgumentException(implode(PHP_EOL, $this->validator->getErrors()));
        }

        helper('form');

        $threadModel = model(ThreadModel::class);

        $data = [
            'threads' => $threadModel->withUsers($threadModel->withTags()->forList($table['search'], $table['perPage'])),
            'table'   => [
                'dropdowns' => [
                    'type' => $this->types,
                ],
                'pager' => $threadModel->pager,
            ],
            'searchUrl'      => route_to('category', $slug),
            'activeCategory' => model(CategoryModel::class)->findBySlug($slug),
        ];

        $data['table'] = [...$table, ...$data['table']];

        return $this->render('discussions/category', $data);
    }

    /**
     * Display a standard forum-style list of discussions.
     */
    public function tag(string $tagSlug)
    {
        $table = [
            'perPage' => $this->request->getGet('perPage') ?? 20,
            'search'  => $this->request->getGet('search') ?? [],
        ];

        $table['search']['tag'] = $tagSlug;

        $rules = [
            'perPage'     => ['in_list[20]'],
            'search.type' => ['permit_empty', 'in_list[' . implode(',', array_keys($this->types)) . ']'],
            'search.tag'  => ['required', 'regex_match[/^[a-z0-9-]{0,20}$/]'],
        ];

        if (! $this->validateData($table, $rules)) {
            throw new InvalidArgumentException(implode(PHP_EOL, $this->validator->getErrors()));
        }

        helper('form');

        $threadModel = model(ThreadModel::class);

        $data = [
            'threads' => $threadModel->withUsers($threadModel->withTags()->forList($table['search'], $table['perPage'])),
            'table'   => [
                'dropdowns' => [
                    'type' => $this->types,
                ],
                'pager' => $threadModel->pager,
            ],
            'searchUrl' => route_to('tag', $tagSlug),
        ];

        $data['table'] = [...$table, ...$data['table']];

        return $this->render('discussions/tag', $data);
    }

    /**
     * Displays a single thread and it's replies.
     */
    public function thread(string $slug): string
    {
        if (! $this->validateData([
            'slug' => $slug,
        ], [
            'slug' => ['max_length[255]'],
        ])) {
            throw new InvalidArgumentException(implode(PHP_EOL, $this->validator->getErrors()));
        }

        $threadModel = model(ThreadModel::class);

        // Find the thread by the slug
        $thread = $threadModel->where('slug', $slug)->withTags()->first();

        if (! $thread) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! $this->request->is('boosted')) {
            // Increase view count
            $threadModel->incrementViewCount($thread->id);
        }

        $postModel = model(PostModel::class);

        if ($postId = (int) $this->request->getGet('post_id')) {
            // User was directed here to see the certain post
            $post = $postModel->where('thread_id', $thread->id)->find($postId);
            // Determine the page number for the post
            $page = $postModel->getPageNumberForPost($post->thread_id, $post->reply_to ?? $post->id);
            // Load all the replies for the post if needed
            if ($page !== null && $post->reply_to !== null) {
                $loadedReplies = [
                    $post->reply_to => $postModel->getAllReplies($post->reply_to),
                ];
            }
        }

        $thread = $threadModel->withUsers($thread);
        $posts  = $postModel->forThread($thread->id, 10, $page ?? null);
        $pager  = $postModel->pager->only(['page']);

        return $this->render('discussions/thread', [
            'slug' => $slug, 'thread' => $thread, 'posts' => $posts,
            'pager' => $pager, 'loadedReplies' => $loadedReplies ?? [],
        ]);
    }
}
