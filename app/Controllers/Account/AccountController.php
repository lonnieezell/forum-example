<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Entities\NotificationSetting;
use App\Models\NotificationSettingModel;
use App\Models\PostModel;
use App\Models\UserModel;
use CodeIgniter\Files\Exceptions\FileNotFoundException;
use CodeIgniter\Shield\Authentication\AuthenticationException;
use ReflectionException;

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
        helper('text');

        $postModel = model(PostModel::class);
        $posts     = $postModel->getPostsByUser(auth()->id(), 10);

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

            if (model(NotificationSettingModel::class)->save($settings)) {
                alerts()->set('success', 'Your notification settings has been saved successfully');
            } else {
                alerts()->set('error', 'Something went wrong');
            }

            return view('account/_notifications', [
                'notification' => model(NotificationSettingModel::class)->find(user_id()),
                'validator'    => $this->validator ?? service('validation'),
            ]);
        }

        return $this->render('account/notifications', [
            'user'         => auth()->user(),
            'notification' => model(NotificationSettingModel::class)->find(user_id()),
            'validator'    => $this->validator ?? service('validation'),
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function profile()
    {
        helper(['form', 'date']);

        if ($this->request->is('post') && $this->validate([
            'name'     => ['permit_empty', 'string', 'max_length[255]'],
            'handed'   => ['required', 'in_list[right,left]'],
            'country'  => ['permit_empty', 'string', 'max_length[2]'],
            'website'  => ['permit_empty', 'valid_url'],
            'location' => ['permit_empty', 'string', 'max_length[255]'],
            'company'  => ['permit_empty', 'string', 'max_length[255]'],
            'signature' => ['permit_empty', 'string', 'max_length[255]'],
        ])) {
            $user = auth()->user();
            $user->fill($this->validator->getValidated());

            model(UserModel::class)->save($user);
            $message = 'Your profile has been updated.';
        }

        return $this->render('account/profile', [
            'user' => auth()->user(),
            'message' => $message ?? null,
            'validator' => $this->validator ?? service('validation'),
        ]);
    }
}
