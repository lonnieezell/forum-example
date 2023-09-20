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
    /**
     * Display a standard forum-style list of discussions.
     */
    public function list(): string
    {
        $table = [
            'perPage' => $this->request->getGet('perPage') ?? 20,
            'search'  => $this->request->getGet('search') ?? [],
        ];

        $types = [
            'recent-threads' => 'by Newest Threads',
            'recent-posts'   => 'by Newest Replies',
            'unanswered'     => 'only Unanswered',
            'my-threads'     => 'only My Threads',
        ];

        $rules = [
            'perPage'     => ['in_list[20]'],
            'search.type' => ['permit_empty', 'in_list[' . implode(',', array_keys($types)) . ']'],
        ];

        if (! $this->validateData($table, $rules)) {
            throw new InvalidArgumentException(implode(PHP_EOL, $this->validator->getErrors()));
        }

        helper('form');

        $threadModel = model(ThreadModel::class);

        $data = [
            'threads' => $threadModel->withTags()->forList($table['search'], $table['perPage']),
            'table'   => [
                'dropdowns' => [
                    'type' => $types,
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
            'perPage'  => $this->request->getGet('perPage') ?? 20,
            'search'   => $this->request->getGet('search') ?? [],
        ];
        $table['search']['category'] = $slug;

        $types = [
            'recent-threads' => 'by Newest Threads',
            'recent-posts'   => 'by Newest Replies',
            'unanswered'     => 'only Unanswered',
            'my-threads'     => 'only My Threads',
        ];

        $rules = [
            'perPage'         => ['in_list[20]'],
            'search.type'     => ['permit_empty', 'in_list[' . implode(',', array_keys($types)) . ']'],
            'search.category' => ['required', 'max_length[255]', 'category_exists[child]']
        ];

        if (! $this->validateData($table, $rules)) {
            throw new InvalidArgumentException(implode(PHP_EOL, $this->validator->getErrors()));
        }

        helper('form');

        $threadModel = model(ThreadModel::class);

        $data = [
            'threads' => $threadModel->withTags()->forList($table['search'], $table['perPage']),
            'table'   => [
                'dropdowns' => [
                    'type' => $types,
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

        $types = [
            'recent-threads' => 'by Newest Threads',
            'recent-posts'   => 'by Newest Replies',
            'unanswered'     => 'only Unanswered',
            'my-threads'     => 'only My Threads',
        ];

        $rules = [
            'perPage'     => ['in_list[20]'],
            'search.type' => ['permit_empty', 'in_list[' . implode(',', array_keys($types)) . ']'],
            'search.tag'  => ['required', 'regex_match[/^[a-z0-9-]{0,20}$/]'],
        ];

        if (! $this->validateData($table, $rules)) {
            throw new InvalidArgumentException(implode(PHP_EOL, $this->validator->getErrors()));
        }

        helper('form');

        $threadModel = model(ThreadModel::class);

        $data = [
            'threads' => $threadModel->withTags()->forList($table['search'], $table['perPage']),
            'table'   => [
                'dropdowns' => [
                    'type' => $types,
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

        $thread    = $threadModel->withUsers($thread);
        $postModel = model(PostModel::class);
        $posts     = $postModel->forThread($thread->id, 10);
        $pager     = $postModel->pager;

        return $this->render('discussions/thread', [
            'slug' => $slug, 'thread' => $thread, 'posts' => $posts, 'pager' => $pager,
        ]);
    }
}
