<?php

namespace App\Concerns;

use App\Libraries\TextFormatter;

trait RendersContent
{
    /**
     * Generates the HTML for this record
     */
    public function render(): string
    {
        return match ($this->markup) {
            'bbcode' => TextFormatter::instance()->renderBBCode($this->body),
            default => TextFormatter::instance()->renderMarkdown($this->body),
        };
    }
}
