<?php

namespace App\Concerns;

trait HasStats
{
    /**
     * Increment stats field.
     */
    public function incrementStats(int $id, string $field): bool
    {
        return $this->builder()->where('id', $id)->increment($field);
    }

    /**
     * Decrement stats field.
     */
    public function decrementStats(int $id, string $field): bool
    {
        return $this->builder()->where('id', $id)->decrement($field);
    }
}
