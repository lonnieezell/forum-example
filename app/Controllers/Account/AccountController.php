<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Entities\NotificationSetting;
use App\Entities\User;
use App\Models\NotificationSettingModel;
use App\Models\PostModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use League\Flysystem\FilesystemException;

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
        helper(['form', 'date', 'number']);

        $maxUploadSize = max_upload_size(true);
        $avatar        = $this->request->getFile('avatar');

        if ($this->request->is('post') && $this->validate([
            'name'      => ['permit_empty', 'string', 'max_length[255]'],
            'handed'    => ['required', 'in_list[right,left]'],
            'country'   => ['permit_empty', 'string', 'max_length[2]'],
            'website'   => ['permit_empty', 'valid_url'],
            'location'  => ['permit_empty', 'string', 'max_length[255]'],
            'company'   => ['permit_empty', 'string', 'max_length[255]'],
            'signature' => ['permit_empty', 'string', 'max_length[255]'],
            'avatar'    => $avatar instanceof UploadedFile
                ? [
                    'uploaded[avatar]',
                    'mime_in[avatar,' . implode(',', config('ImageUpload')->fileMime) . ']',
                    "max_size[avatar,{$maxUploadSize}]",
                ]
                : 'permit_empty',
        ])) {
            /** @var User $user */
            $user = auth()->user();
            $user->fill($this->validator->getValidated());

            // If we have a new avatar, delete the old one first,
            // then save the new one to the configured Storage.
            if ($avatar && $avatar->isValid()) {
                try {
                    $user->deleteAvatar();
                    $user->avatar = $user->saveAvatar($this->request->getFile('avatar'));
                } catch (FilesystemException $e) {
                    alerts()->set('error', $e->getMessage());
                }
            }

            if (model(UserModel::class)->save($user)) {
                alerts()->set('success', 'Your profile has been updated');
            } else {
                alerts()->set('error', 'Something went wrong');
            }

            // Reload the page to ensure the avatar is displayed correctly in all cases.
            return redirect()->hxRefresh();
        }

        $view = $this->request->is('post') ? 'account/_profile' : 'account/profile';

        return $this->render($view, [
            'user'      => auth()->user(),
            'validator' => $this->validator ?? service('validation'),
            'maxUpload' => number_to_size($maxUploadSize * 1000),
        ]);
    }

    /**
     * Delete a user's avatar.
     */
    public function deleteAvatar()
    {
        if (! $this->policy->can('users.deleteAvatar', auth()->user())) {
            return alerts()->set('error', 'You do not have permission to delete this avatar');
        }

        /** @var User $user */
        $user = auth()->user();

        try {
            $user->deleteAvatar();

            if (model(UserModel::class)->save($user)) {
                alerts()->set('success', 'Your avatar has been deleted');
            } else {
                alerts()->set('error', 'Something went wrong');
            }
        } catch (FilesystemException $e) {
            alerts()->set('error', $e->getMessage());
        } finally {
            return redirect()->hxRefresh();
        }
    }
}
