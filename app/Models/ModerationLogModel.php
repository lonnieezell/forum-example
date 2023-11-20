<?php

namespace App\Models;

use App\Entities\ModerationLog;
use App\Enums\ModerationLogStatus;
use CodeIgniter\Model;
use ReflectionException;

class ModerationLogModel extends Model
{
    protected $table            = 'moderation_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = ModerationLog::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['resource_id', 'resource_type', 'status', 'author_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    /**
     * @throws ReflectionException
     */
    public function add(string $resourceType, ModerationLogStatus $status, array $items, int $userId): bool|int
    {
        $logs = [];

        foreach ($items as $key) {
            $logs[] = new ModerationLog([
                'resource_id' => $key,
                'resource_type' => $resourceType,
                'status' => $status->value,
                'author_id' => $userId,
            ]);
        }

        return $this->insertBatch($logs);
    }
}
