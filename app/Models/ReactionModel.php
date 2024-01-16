<?php

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;

/**
 * Handles the reactions to posts and threads.
 *
 * At this time we're only counting "likes" on a post,
 * but the model is designed in a way that we can add more reactions later.
 */
class ReactionModel extends Model
{
    final public const REACTION_LIKE = 1;

    protected $table            = 'reactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['post_id', 'thread_id', 'reactor_id', 'reaction'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * React to a post or thread.
     */
    public function reactTo(int $userId, int $resourceId, string $resourceType, int $reaction): void
    {
        // Can only react to posts and threads. Users are updated automatically.
        if (! in_array($resourceType, ['post', 'thread'], true)) {
            throw new RuntimeException('Cannot react to this.');
        }

        $resource = $resourceType === 'thread'
            ? model(ThreadModel::class)->find($resourceId)
            : model(PostModel::class)->find($resourceId);

        if (! $resource) {
            throw new RuntimeException('Cannot react to this. Resource not found.');
        }

        // Has the user already reacted to this resource?
        $reactionModel    = model(ReactionModel::class);
        $existingReaction = $reactionModel
            ->where('reactor_id', $userId)
            ->where("{$resourceType}_id", $resourceId)
            ->first();

        if ($existingReaction) {
            // If the user is trying to react to the same resource with the same reaction,
            // then we'll just remove the reaction.
            if ($existingReaction->reaction === $reaction) {
                $reactionModel->delete($existingReaction->id);

                // Decrement count from the resource and the resources's user.
                $resource->decrementReactionCount();
                $resource->author()->decrementReactionCount();

                return;
            }

            // Otherwise, we'll update the reaction to the new one.
            $existingReaction->reaction = $reaction;
            $reactionModel->save($existingReaction);

            return;
        }

        // Otherwise, we'll create a new reaction.
        $reactionModel->insert([
            'reactor_id'         => $userId,
            "{$resourceType}_id" => $resourceId,
            'reaction'           => $reaction,
        ]);

        // Increment count from the resource and the resources's user.
        $resource->incrementReactionCount();
        $resource->author()->incrementReactionCount();
    }

    /**
     * Returns the number of reactions for a resource.
     */
    public function countReactions(int $resourceId, string $resourceType): int
    {
        return $this
            ->when($resourceType === 'thread', static fn ($query) => $query->where('thread_id', $resourceId))
            ->when($resourceType === 'post', static fn ($query) => $query->where('post_id', $resourceId))
            ->countAllResults();
    }
}
