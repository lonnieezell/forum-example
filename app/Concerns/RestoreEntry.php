<?php

namespace App\Concerns;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\I18n\Time;

trait RestoreEntry
{
    /**
     * Callbacks for beforeRestore
     *
     * @var array
     */
    protected $beforeRestore = [];

    /**
     * Callbacks for afterRestore
     *
     * @var array
     */
    protected $afterRestore = [];

    /**
     * Restores a single record from the database where $id matches.
     *
     * @return BaseResult|bool
     *
     * @throws DatabaseException
     */
    public function restore(int $id, Time $deletedAt)
    {
        $data = [
            'deleted_at' => null,
        ];

        if ($this->useTimestamps && $this->updatedField && ! array_key_exists($this->updatedField, $data)) {
            $data[$this->updatedField] = $this->setDate();
        }

        $eventData = [
            'id'        => $id,
            'data'      => $data,
            'deletedAt' => $deletedAt,
        ];

        if ($this->tempAllowCallbacks) {
            $this->trigger('beforeRestore', $eventData);
        }

        $eventData = [
            'id'        => $id,
            'data'      => $eventData['data'],
            'deletedAt' => $eventData['deletedAt'],
            'result'    => $this->doUpdate([$id], $eventData['data']),
        ];

        if ($this->tempAllowCallbacks) {
            $this->trigger('afterRestore', $eventData);
        }

        $this->tempAllowCallbacks = $this->allowCallbacks;

        return $eventData['result'];
    }
}
