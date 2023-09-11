<?php

namespace App\Controllers\Discussions;

/**
 * Class Home
 */
class DiscussionController extends \App\Controllers\BaseController
{
    /**
     * Display a standard forum-style list of discussions.
     */
    public function list()
    {
        return $this->render('discussions/list');
    }

    /**
     * Displays a single thread and it's replies.
     */
    public function thread(string $slug)
    {
        return $this->render('discussions/thread', [
            'slug' => $slug,
        ]);
    }
}
