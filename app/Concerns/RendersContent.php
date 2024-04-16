<?php

namespace App\Concerns;

use App\Libraries\TextFormatter;
use DOMDocument;

trait RendersContent
{
    /**
     * Generates the HTML for this record
     */
    public function render(): string
    {
        $cacheKey = $this->cacheKey('-body');

        if (! $content = cache($cacheKey)) {
            $content = match ($this->markup) {
                'bbcode' => TextFormatter::instance()->renderBBCode($this->body),
                default  => TextFormatter::instance()->renderMarkdown($this->body),
            };

            $content = $this->nofollowLinks($content);

            cache()->save($cacheKey, $content, MONTH);
        }

        return $content;
    }

    /**
     * Strips all anchor tags from the given HTML.
     *
     * Used mostly to strip out links from the rendered content
     * when not allowed by the user's trust level.
     */
    public function stripAnchors(string $html): string
    {
        $xml = new DOMDocument();
        $xml->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        foreach ($xml->getElementsByTagName('a') as $anchor) {
            $anchor->parentNode->replaceChild($xml->createTextNode($anchor->nodeValue), $anchor);
        }

        return $xml->saveHTML();
    }

    /**
     * Adds `rel="nofollow"` to all anchor tags in the given HTML.
     * Also ensures all links open in a new tab.
     *
     * Used for most user-generated content to prevent spam.
     */
    public function nofollowLinks(string $html): string
    {
        $xml = new DOMDocument();
        $xml->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        foreach ($xml->getElementsByTagName('a') as $anchor) {
            $anchor->setAttribute('rel', 'nofollow');
            $anchor->setAttribute('target', '_blank');
        }

        return $xml->saveHTML();
    }
}
