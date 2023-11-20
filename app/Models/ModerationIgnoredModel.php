<?php

namespace App\Models;

use App\Entities\ModerationIgnored;
use CodeIgniter\Model;
use ReflectionException;

class ModerationIgnoredModel extends Model
{
    protected $table            = 'moderation_ignored';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = ModerationIgnored::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['moderation_report_id', 'user_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    /**
     * Ignore item.
     *
     * @throws ReflectionException
     */
    public function ignore(array $items, int $userId): bool|int
    {
        $ignored = [];

        foreach ($items as $key) {
            $ignored[] = new ModerationIgnored([
                'moderation_report_id' => $key,
                'user_id'              => $userId,
            ]);
        }

        return $this->insertBatch($ignored);
    }

    /**
     * Get ignored IDs.
     */
    public function getIgnored(int $userId): array
    {
        $ignored = $this->select('moderation_report_id')
            ->where('user_id', $userId)
            ->findAll();

        return array_column($ignored, 'moderation_report_id');
    }
}
