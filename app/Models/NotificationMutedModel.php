<?php

namespace App\Models;

use App\Entities\NotificationMuted;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\I18n\Time;
use Config\Database;

class NotificationMutedModel
{
    protected string $table = 'notification_muted';
    protected BaseConnection $db;

    public function __construct(?ConnectionInterface $db)
    {
        /**
         * @var BaseConnection|null $db
         */
        $db ??= Database::connect();

        $this->db = $db;
    }

    public function findBy(string $field, int $value): array
    {
        return $this->db
            ->table($this->table)
            ->where($field, $value)
            ->get()
            ->getCustomResultObject(NotificationMuted::class);
    }

    public function find(int $userId, int $threadId)
    {
        return $this->db
            ->table($this->table)
            ->where('user_id', $userId)
            ->where('thread_id', $threadId)
            ->get()
            ->getCustomRowObject(0, NotificationMuted::class);
    }

    public function insert(int $userId, int $threadId): int
    {
        $this->db
            ->table($this->table)
            ->ignore()
            ->insert([
                'user_id'    => $userId,
                'thread_id'  => $threadId,
                'created_at' => Time::now(),
            ]);

        return (int) $this->db->insertID();
    }

    public function delete(int $userId, int $threadId): bool
    {
        return (bool) $this->db
            ->table($this->table)
            ->where('user_id', $userId)
            ->where('thread_id', $threadId)
            ->delete();
    }
}
