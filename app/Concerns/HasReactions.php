<?php

namespace App\Concerns;

trait HasReactions
{
    public function incrementReactionCount(): void
    {
        $class = explode('\\', get_class($this));
        $class = array_pop($class);
        $model = model("{$class}Model");
        $model->where('id', $this->id)->increment('reaction_count');
    }

    public function decrementReactionCount(): void
    {
        $class = explode('\\', get_class($this));
        $class = array_pop($class);
        $model = model("{$class}Model");
        $model->where('id', $this->id)->decrement('reaction_count');
    }
}
