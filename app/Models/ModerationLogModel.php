<?php

namespace App\Models;

use App\Entities\ModerationLog;
use App\Enums\ModerationLogStatus;
use CodeIgniter\I18n\Time;
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
                'resource_id'   => $key,
                'resource_type' => $resourceType,
                'status'        => $status->value,
                'author_id'     => $userId,
            ]);
        }

        return $this->insertBatch($logs);
    }

    public function list(array $search, int $page, int $perPage, string $sortColumn, string $sortDirection): ?array
    {
        if (isset($search['createdAtRange'])) {
            $search['createdAtRange'] = explode(' - ', (string) $search['createdAtRange']);
        }

        $results = $this
            ->when(
                isset($search['resourceType']) && $search['resourceType'] !== '',
                static fn ($query) => $query->where('resource_type', $search['resourceType'])
            )
            ->when(
                isset($search['status']) && $search['status'] !== '',
                static fn ($query) => $query->where('status', $search['status'])
            )
            ->when(
                isset($search['authorId']) && $search['authorId'] !== '',
                static fn ($query) => $query->where('author_id', $search['authorId'])
            )
            ->when(
                isset($search['createdAt']) && $search['createdAt'] !== '',
                static fn ($query) => match ((string) $search['createdAt']) {
                    'today'      => $query->where('DATE(created_at)', Time::now()->format('Y-m-d')),
                    'yesterday'  => $query->where('DATE(created_at)', Time::now()->subDays(1)->format('Y-m-d')),
                    'last7Days'  => $query->where('DATE(created_at) >=', Time::now()->subDays(7)->format('Y-m-d')),
                    'last30Days' => $query->where('DATE(created_at) >=', Time::now()->subDays(30)->format('Y-m-d')),
                    'thisYear'   => $query->where('YEAR(created_at)', Time::now()->format('Y')),
                    'custom'     => $query->when(
                        isset($search['createdAtRange']) && count($search['createdAtRange']) === 2,
                        static fn ($subQuery) => $subQuery->where('DATE(created_at) >=', $search['createdAtRange'][0])->where('DATE(created_at) <=', $search['createdAtRange'][1])
                    ),
                },
            )
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage, 'default', $page);

        if (empty($results)) {
            return [];
        }

        return $results;
    }

    /**
     * Get author IDs.
     */
    public function getAuthorIds(): array
    {
        $results = $this->builder()->distinct()->select('author_id')->get()->getResultArray();

        return array_map('intval', array_column($results, 'author_id'));
    }
}
