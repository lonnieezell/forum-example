<?php

namespace App\Concerns;

trait HasReactions
{
    public function incrementReactionCount(): void
    {
        $class = explode('\\', (string) $this::class);
        $class = array_pop($class);
        $model = model("{$class}Model");
        $model->where('id', $this->id)->increment('reaction_count');
    }

    public function decrementReactionCount(): void
    {
        $class = explode('\\', (string) $this::class);
        $class = array_pop($class);
        $model = model("{$class}Model");
        $model->where('id', $this->id)->decrement('reaction_count');
    }
}
