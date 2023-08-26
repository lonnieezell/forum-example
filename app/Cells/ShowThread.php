<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;
use App\Models\ThreadModel;
use App\Models\PostModel;

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

        // Find the thread by the slug
        $this->thread = $threadModel->where('slug', $slug)->first();
        $this->thread = $threadModel->withUsers($this->thread);

        if ($this->thread) {
            $this->posts = $postModel->forThread($this->thread->id, 10);
            $this->pager = $postModel->pager;
        }
    }
}
