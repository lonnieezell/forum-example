<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Entities\NotificationSetting;
use App\Models\NotificationSettingModel;
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

    /**
     * Manage notifications about new replies
     * for thread and posts.
     */
    public function notifications()
    {
        helper('form');

        if ($this->request->is('post') && $this->validate([
            'email_thread'     => ['required', 'in_list[0,1]'],
            'email_post'       => ['required', 'in_list[0,1]'],
            'email_post_reply' => ['required', 'in_list[0,1]'],
        ])) {
            $settings          = new NotificationSetting($this->validator->getValidated());
            $settings->user_id = user_id();

            model(NotificationSettingModel::class)->save($settings);
        }

        return $this->render('account/notifications', [
            'user'         => auth()->user(),
            'notification' => model(NotificationSettingModel::class)->find(user_id()),
            'validator'    => $this->validator ?? service('validation'),
        ]);
    }
}
