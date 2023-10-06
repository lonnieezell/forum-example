<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Models\PostModel;

class AccountController extends BaseController
{
    /**
     * Display the main user account page.
     */
    public function index()
    {
        return redirect()->route('account-posts');
    }

    /**
     * Display the posts the user is part of,
     * in reverse chronological order.
     */
    public function posts()
    {
        $postModel = model(PostModel::class);
        $posts     = $postModel
            ->where('author_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return $this->render('account/posts', [
            'user'  => auth()->user(),
            'posts' => $posts,
            'pager' => $postModel->pager,
        ]);
    }
}
