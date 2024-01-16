<?php

namespace App\Concerns;

use App\Entities\User;
use App\Models\UserModel;
use CodeIgniter\Entity\Entity;

trait HasAuthorsAndEditors
{
    /**
     * Adds the author and editor to each post.
     */
    public function withUsers(array|Entity|null $records): array|Entity|null
    {
        if (empty($records)) {
            return $records;
        }

        $wasSingle = ! is_array($records);
        if (! is_array($records)) {
            $records = [$records];
        }

        $userIds = [];

        foreach ($records as $record) {
            $userIds[] = $record->author_id;
            $userIds[] = $record->editor_id;
        }

        $users = model(UserModel::class)
            ->whereIn('id', array_unique($userIds))
            ->findAll();

        // Convert the array of users into an associative array
        // with the user ID as the key.
        $userIds = array_map(static fn ($user) => $user->id, $users);
        $users   = array_combine($userIds, $users);

        // Add the users to the records
        array_walk($records, static function (&$post) use ($users) {
            $post->author = $users[$post->author_id] ?? null;
            $post->editor = $users[$post->editor_id] ?? null;
        });

        return $wasSingle
            ? array_shift($records)
            : $records;
    }

    /**
     * Returns the author of the resource.
     */
    public function author(): ?User
    {
        if (empty($this->author_id)) {
            return null;
        }

        if (empty($this->author)) {
            $this->author = model(UserModel::class)->find($this->author_id);
        }

        return $this->author;
    }

    /**
     * Is the current user the author of this resource?
     */
    public function isOwner(?User $user = null): bool
    {
        if (! $user) {
            // Must be logged in to check if no user provided.
            if (! auth()->loggedIn()) {
                return false;
            }

            // Otherwise use the logged in user.
            $user = auth()->user();
        }

        return $this->author_id === $user->id;
    }
}
