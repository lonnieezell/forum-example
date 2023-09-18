<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
use App\Models\ThreadModel;
use InvalidArgumentException;

/**
 * Class Home
 */
class TagController extends BaseController
{
    /**
     * Display a standard forum-style list of discussions.
     */
    public function get(string $tagSlug)
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
            'search.tag'  => ['required', 'regex_match[/^[a-z0-9-]{0,20}$/]']
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

        return $this->render('discussions/tags/get', $data);
    }
}
