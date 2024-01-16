<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
use App\Models\PostModel;
use App\Models\ReactionModel;
use App\Models\ThreadModel;

class ReactionController extends BaseController
{
    /**
     * Toggle a reaction on a post or thread.
     */
    public function toggleReaction(int $resourceId, string $resourceType)
    {
        $model = model(ReactionModel::class);
        $model->reactTo(auth()->id(), $resourceId, $resourceType, ReactionModel::REACTION_LIKE);

        $record = $resourceType === 'thread' ? model(ThreadModel::class) : model(PostModel::class);
        $record = $record
            ->includeHasReacted(auth()->id())
            ->find($resourceId);

        return $this->render('discussions/_reactions', [
            'record' => $record,
        ]);
    }
}
