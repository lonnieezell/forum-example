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
        switch ($this->markup) {
            case 'bbcode':
                return TextFormatter::instance()->renderBBCode($this->body);

            case 'markdown':
            default:
                return TextFormatter::instance()->renderMarkdown($this->body);
        }
    }
}
