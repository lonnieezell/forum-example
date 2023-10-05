<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;

class AccountNavCell extends Cell
{
    protected string $view = 'account_nav_cell';
    public array $pages;

    public function mount()
    {
        $this->pages = [
            [
                'title' => 'Posts',
                'url'   => route_to('account-posts'),
                'icon'  => view('icons/chat-bubbles'),
            ],
            [
                'title' => 'Discussions',
                'url'   => '',
                'icon'  => view('icons/chat-bubble'),
            ],
            [
                'title' => 'Notifications',
                'url'   => '',
                'icon'  => view('icons/bell'),
            ],
            [
                'title' => 'Settings',
                'url'   => '',
                'icon'  => view('icons/gear'),
            ],
            [
                'title' => 'Security',
                'url'   => '',
                'icon'  => view('icons/shield'),
            ],
        ];
    }
}
