<?php

namespace App\Models;

use App\Concerns\HasAuthorsAndEditors;
use App\Concerns\HasThreadsAndPosts;
use App\Entities\ModerationLog;
use App\Entities\ModerationReport;
use App\Enums\ModerationLogStatus;
use CodeIgniter\Model;
use ReflectionException;

class ModerationReportModel extends Model
{
    use HasAuthorsAndEditors;
    use HasThreadsAndPosts;

    protected $table            = 'moderation_reports';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = ModerationReport::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['resource_id', 'resource_type', 'comment', 'author_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    /**
     * List moderation queue.
     */
    public function list(int $userId, string $resourceType, int $page, int $perPage, string $sortColumn, string $sortDirection): array
    {
        $ignored = model(ModerationIgnoredModel::class)->getIgnored($userId);

        $selects = [
            '*',
        ];

        $results = $this
            ->select(implode(', ', $selects))
            ->where('resource_type', $resourceType)
            ->when($ignored !== [], static function ($query) use ($ignored) {
                $query->whereNotIn('id', $ignored);
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage, 'default', $page);

        $results = $this->withThreadsAndPosts($results);
        $results = $this->withUsers($results);

        return $results;
    }

    /**
     * Delete the report and save the performed action in the logs.
     *
     * @throws ReflectionException
     */
    public function action(string $resourceType, ModerationLogStatus $status, array $items, int $userId): bool|int
    {
        $this->delete($items);

        // Only with this status we have to touch the thread / post
        if (ModerationLogStatus::DENIED === $status) {
            $model = model(ucfirst($resourceType) . 'Model');
            // Delete one by one to trigger the model event
            foreach ($items as $item) {
                $model->delete($item);
            }
        }

        return model(ModerationLogModel::class)->add($resourceType, $status, $items, $userId);
    }
}
