<?php

namespace App\Concerns;

use App\Models\UserModel;
use CodeIgniter\Entity\Entity;
use CodeIgniter\Shield\Authentication\Authenticators\Session;

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

            // Get email to grab avatar later
            if ($identityAuthor = $post->author?->getIdentities(Session::ID_TYPE_EMAIL_PASSWORD)[0] ?? null) {
                $post->author->setEmail($identityAuthor->secret);
            }

            // Get email to grab avatar later
            if ($identityEditor = $post->editor?->getIdentities(Session::ID_TYPE_EMAIL_PASSWORD)[0] ?? null) {
                $post->editor->setEmail($identityEditor->secret);
            }
        });

        return $wasSingle
            ? array_shift($records)
            : $records;
    }
}
