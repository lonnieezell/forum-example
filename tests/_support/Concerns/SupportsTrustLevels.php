<?php

namespace Tests\Support\Concerns;

use App\Entities\User;
use App\Models\Factories\PostFactory;
use App\Models\Factories\ThreadFactory;
use App\Models\Factories\UserFactory;
use App\Models\ReactionModel;
use CodeIgniter\I18n\Time;

trait SupportsTrustLevels
{
    /**
     * Creates a new post and likes it for the user.
     * Used to test the user giving likes.
     */
    protected function userLikesPosts(User $user, int $count)
    {
        $reactions = model(ReactionModel::class);
        $thread = fake(ThreadFactory::class);

        for ($i = 0; $i < $count; $i++) {
            // Create a new fake post and like it
            $post = fake(PostFactory::class, ['thread_id' => $thread->id]);
            $reactions->reactTo($user->id, $post->id, 'post', ReactionModel::REACTION_LIKE);
        }
    }

    /**
     * Creates a new post and has someone else like it.
     * Used to test the user receiving likes.
     */
    protected function userLikedByOthers(User $user, int $count)
    {
        $reactions = model(ReactionModel::class);
        $reactor = fake(UserFactory::class);
        $thread = fake(ThreadFactory::class);

        for ($i = 0; $i < $count; $i++) {
            // Create a new fake post and like it
            $post = fake(PostFactory::class, ['author_id' => $user->id, 'thread_id' => $thread->id]);
            $reactions->reactTo($reactor->id, $post->id, 'post', ReactionModel::REACTION_LIKE);
        }
    }

    /**
     * Creates a number of visits for the user.
     */
    protected function userVisits(User $user, int $count)
    {
        $builder = db_connect()->table('user_visits');

        for ($i = 0; $i < $count; $i++) {
            $builder->insert([
                'user_id' => $user->id,
                'visited_on' => Time::now()->subDays($i)->toDateString(),
            ]);
        }
    }
}
