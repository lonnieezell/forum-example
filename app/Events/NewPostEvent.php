<?php

namespace App\Events;

use App\Entities\Post;
use App\Entities\Thread;
use App\Entities\User;
use App\Models\NotificationSettingModel;
use App\Models\PostModel;

class NewPostEvent
{
    /**
     * Number of notifications sent.
     */
    private int $count = 0;

    public function __construct(protected Thread $thread, protected Post $post)
    {
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
     *
     * @todo Make it run in the background (queueNotification).
     */
    private function sendNotification(User $recipient, Thread $thread, Post $post): bool
    {
        helper('text');

        return service('email', false)
            ->setFrom('notifications@myth.forum', config('App')->siteName)
            ->setTo($recipient->email)
            ->setSubject(config('App')->siteName . ' - New post notification')
            ->setMessage(view('_emails/new_post', [
                'user' => $recipient, 'thread' => $thread, 'post' => $post,
            ]))
            ->send();
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

        $this->count++;

        // send notification
        return $this->sendNotification($this->thread->author, $this->thread, $this->post);
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
            // user wants to be notified about every reply
            if ($setting->email_post === true) {
                // send notification
                $this->sendNotification($this->post->author, $this->thread, $this->post);
                $this->count++;

                continue;
            }

            // user wants to be notified about replies to his posts only
            if ($setting->email_post_reply === true) {
                // direct reply to the post author
                if ($postReplyTo?->author_id === $setting->user_id) {
                    // send notification
                    $this->sendNotification($this->post->author, $this->thread, $this->post);
                    $this->count++;

                    continue;
                }

                // user replied to the same post earlier
                if (in_array($setting->user_id, $replyAuthorIds, true)) {
                    // send notification
                    $this->sendNotification($this->post->author, $this->thread, $this->post);
                    $this->count++;
                }
            }
        }

        return $this->count > 0;
    }
}
