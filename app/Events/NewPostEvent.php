<?php

namespace App\Events;

use App\Entities\Category;
use App\Entities\Post;
use App\Entities\Thread;
use App\Entities\User;
use App\Models\NotificationMutedModel;
use App\Models\NotificationSettingModel;
use App\Models\PostModel;
use App\Models\UserModel;

class NewPostEvent
{
    /**
     * Number of notifications sent.
     */
    private int $count = 0;

    /**
     * Notified users IDs.
     */
    private array $notifiedUsers = [];

    /**
     * Muted users for thread.
     */
    private array $mutedUsers;

    public function __construct(protected Category $category, protected Thread $thread, protected Post $post)
    {
        $this->mutedUsers = model(NotificationMutedModel::class)->findBy('thread_id', $thread->id);
        $this->mutedUsers = array_column($this->mutedUsers, 'user_id');
    }

    /**
     * Get number of notifications sent.
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Process event.
     */
    public function process(): void
    {
        if ($this->thread->author_id !== $this->post->author_id) {
            // prepare notification settings for thread author
            $this->handleThreadNotifications();
        }

        // prepare notification settings for post authors
        $this->handlePostNotifications();
    }

    /**
     * Send email notification.
     */
    private function sendNotification(User $user, Category $category, Thread $thread, Post $post): bool
    {
        helper('text');

        return service('queue')->push('emails', 'email-simple-message', [
            'to'      => $user->email,
            'subject' => 'New post notification',
            'message' => view('_emails/email_post_notification', [
                'user' => $user, 'category' => $category, 'thread' => $thread, 'post' => $post,
            ]),
        ]);
    }

    /**
     * Handle thread related notifications.
     */
    private function handleThreadNotifications(): bool
    {
        $notifications = model(NotificationSettingModel::class)
            ->withThreadNotification()
            ->find($this->thread->author_id);

        if (empty($notifications)) {
            return false;
        }

        // skip users who have muted notifications for this thread
        if (in_array($this->thread->author_id, $this->mutedUsers, true)) {
            return false;
        }

        $this->count++;
        $this->notifiedUsers[] = $this->thread->author_id;

        // send notification
        return $this->sendNotification($this->thread->author, $this->category, $this->thread, $this->post);
    }

    /**
     * Handle post related notifications.
     */
    private function handlePostNotifications(): bool
    {
        $postModel = model(PostModel::class);

        // get all post authors except the current one
        $authorIds = $postModel->getAuthorIds($this->thread->id, [$this->post->author_id]);

        if (empty($authorIds)) {
            return false;
        }

        // get notification settings for the post authors
        $notifications = model(NotificationSettingModel::class)->withPostNotification()->find($authorIds);

        if (empty($notifications)) {
            return false;
        }

        $users = model(UserModel::class)->find(array_column($notifications, 'user_id'));
        $users = array_column($users, null, 'id');

        // if we deal with the reply_to post type get the main post
        $postReplyTo = $this->post->reply_to !== null ?
            $postModel->find($this->post->reply_to) :
            null;
        // get authors of all replies for the main post
        $replyAuthorIds = $postReplyTo !== null ?
            $postModel->getReplyAuthorIds($this->post->reply_to, [$this->post->author_id]) :
            [];

        // check users notifications
        foreach ($notifications as $setting) {
            // skip users who have muted notifications for this thread
            if (in_array($setting->user_id, $this->mutedUsers, true)) {
                continue;
            }
            // skip if user was already notified
            if (in_array($setting->user_id, $this->notifiedUsers, true)) {
                continue;
            }
            // user wants to be notified about every reply
            if ($setting->email_post === true && isset($users[$setting->user_id])) {
                // send notification
                $this->sendNotification($users[$setting->user_id], $this->category, $this->thread, $this->post);
                $this->count++;
                $this->notifiedUsers[] = $setting->user_id;

                continue;
            }

            // user wants to be notified about replies to his posts only
            if ($setting->email_post_reply === true) {
                // direct reply to the post author
                if ($postReplyTo?->author_id === $setting->user_id) {
                    // send notification
                    $this->sendNotification($users[$setting->user_id], $this->category, $this->thread, $this->post);
                    $this->count++;
                    $this->notifiedUsers[] = $setting->user_id;

                    continue;
                }

                // user replied to the same post earlier
                if (in_array($setting->user_id, $replyAuthorIds, true) && isset($users[$setting->user_id])) {
                    // send notification
                    $this->sendNotification($users[$setting->user_id], $this->category, $this->thread, $this->post);
                    $this->count++;
                    $this->notifiedUsers[] = $setting->user_id;
                }
            }
        }

        return $this->count > 0;
    }
}
