<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;

class ShowThread extends Cell
{
    protected string $view = 'show_thread_cell';

    public $thread;
    public $posts;
    public $pager;

    public function mount(string $slug)
    {
        $threadModel = model(ThreadModel::class);
        $postModel = model(PostModel::class);

        $this->thread = $threadModel->findBySlug($slug);
        $this->thread = $threadModel->withUsers($this->thread);
        $this->posts = $postModel->forThread($this->thread->id, 10);
        $this->pager = $postModel->pager;
    }
}
