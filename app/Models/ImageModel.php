<?php

namespace App\Models;

use App\Entities\Image;
use CodeIgniter\Model;

class ImageModel extends Model
{
    protected $table            = 'images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Image::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'thread_id', 'post_id', 'name', 'size', 'is_used', 'ip_address'];

    // Dates
    protected $useTimestamps = true;

    /**
     * Find new images for user or already assigned to the given thread/post.
     */
    public function findForCheck(int $userId, int $threadId, ?int $postId = null)
    {
        return $this
            ->where('user_id', $userId)
            ->groupStart()
            ->groupStart()
            ->where('thread_id', $threadId)
            ->where('post_id', $postId)
            ->groupEnd()
            ->orWhere('thread_id', null)
            ->groupEnd()
            ->find();
    }
}
