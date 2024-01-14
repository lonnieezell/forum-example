<?php

namespace App\Cells;

use App\Entities\Post;
use App\Entities\Thread;
use App\Entities\User;
use App\Libraries\Policies\Policy;
use App\Models\ThreadModel;
use CodeIgniter\View\Cells\Cell;

/**
 * Displays the action bar for all posts and threads.
 */
class ActionBarCell extends Cell
{
    public Post|Thread $record;
    public ?Thread $thread = null; // needed for answering posts
    public string $type;
    public ?User $user     = null;
    public int $reactionCount = 0;

    protected string $view = 'action_bar_cell';
    private Policy $policy;

    public function mount(Post|Thread $record, ?User $user = null): void
    {
        $this->record = $record;
        $this->thread = null;
        $this->type   = $record instanceof Post ? 'post' : 'thread';
        $this->user   = $user ?? auth()->user();

        $this->thread = $record instanceof Post
            ? $record->thread
            : $record;
        if (empty($this->thread) && ! empty($this->record->thread_id)) {
            $this->thread = model(ThreadModel::class)->find($this->record->thread_id);
        }

        $this->policy = service('policy');
        if ($this->user instanceof User) {
            $this->policy->withUser($this->user);
        }
    }

    public function isPost(): bool
    {
        return $this->type === 'post';
    }

    public function isThread(): bool
    {
        return $this->type === 'thread';
    }

    /**
     * Can the user edit this recored?
     */
    public function canEdit(): bool
    {
        if ($this->isPost() && $this->record->isMarkedAsDeleted()) {
            return false;
        }

        return $this->policy->can("{$this->type}s.edit", $this->record);
    }

    /**
     * Can the user delete this record?
     */
    public function canDelete(): bool
    {
        if ($this->isPost() && $this->record->isMarkedAsDeleted()) {
            return false;
        }

        return $this->policy->can("{$this->type}s.delete", $this->record);
    }

    /**
     * Can the user reply to this thread?
     */
    public function canReply(): bool
    {
        return $this->policy->can('posts.create');
    }

    /**
     * Can the user report this record?
     */
    public function canReport(): bool
    {
        // A user cannot report on themselves.
        if ($this->record->author_id === $this->user->id) {
            return false;
        }

        return $this->policy->can('content.report', $this->record);
    }

    /**
     * Can the user manage the answer for this thread?
     */
    public function canManageAnswer(): bool
    {
        return $this->isPost()
            && $this->policy->can('threads.manageAnswer', $this->thread);
    }
}
