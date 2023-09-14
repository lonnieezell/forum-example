<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
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
            'threads' => $threadModel->forList($table['search'], $table['perPage']),
            'table'   => [
                'dropdowns' => [
                    'type' => $types,
                ],
                'pager' => $threadModel->pager,
            ],
        ];

        $data['table'] = [...$table, ...$data['table']];

        return $this->render('discussions/list', $data);
    }

    /**
     * Displays a single thread and it's replies.
     */
    public function thread(string $slug): string
    {
        if (! $this->validateData([
            'slug' => $slug
        ], [
            'slug' => ['max_length[255]'],
        ])) {
            throw new InvalidArgumentException(implode(PHP_EOL, $this->validator->getErrors()));
        }

        $threadModel = model(ThreadModel::class);

        // Find the thread by the slug
        $thread = $threadModel->where('slug', $slug)->first();

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
