<?php

namespace App\Libraries;

use ErrorException;
use s9e\TextFormatter\Configurator;

class TextFormatter
{
    private static ?TextFormatter $instance = null;
    private $markdownParser;
    private $markdownRenderer;
    private $bbcodeParser;
    private $bbcodeRenderer;

    public static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function renderMarkdown(string $text): string
    {
        // If it's just plain text, convert it to the
        // intermediate XML format used by the parser
        try {
            if (! simplexml_load_string($text)) {
                $text = $this->markdownParser()->parse($text);
            }
        } catch (ErrorException) {
            $text = $this->markdownParser()->parse($text);
        }

        return $this->markdownRenderer()->render($text);
    }

    public function renderBBCode(string $text): string
    {
        // If it's just plain text, convert it to the
        // intermediate XML format used by the parser
        if (! simplexml_load_string($text)) {
            $text = $this->bbcodeParser()->parse($text);
        }

        return $this->bbcodeRenderer()->render($text);
    }

    /**
     * Returns the Markdown parser
     *
     * @return mixed
     */
    private function markdownParser()
    {
        if ($this->markdownParser !== null) {
            return $this->markdownParser;
        }

        $configurator = new Configurator();
        $configurator = $this->loadStandardPlugins($configurator);
        $configurator->plugins->load('Litedown');
        $configurator->plugins->load('PipeTables');
        $configurator->TaskLists;

        // Get an instance of the parser and the renderer
        extract($configurator->finalize());

        $this->markdownParser   = $parser;
        $this->markdownRenderer = $renderer;

        return $this->markdownParser;
    }

    /**
     * Returns the Markdown renderer
     *
     * @return mixed
     */
    private function markdownRenderer()
    {
        if ($this->markdownRenderer !== null) {
            return $this->markdownRenderer;
        }

        $this->markdownParser();

        return $this->markdownRenderer;
    }

    /**
     * Returns the BBCode parser
     *
     * @return mixed
     */
    private function bbcodeParser()
    {
        if ($this->bbcodeParser !== null) {
            return $this->bbcodeParser;
        }

        $configurator = new Configurator();
        $configurator->plugins->load('Bbcodes');

        // Get an instance of the parser and the renderer
        extract($configurator->finalize());

        $this->bbcodeParser   = $parser;
        $this->bbcodeRenderer = $renderer;

        return $this->bbcodeParser;
    }

    /**
     * Returns the BBCode renderer
     *
     * @return mixed
     */
    private function bbcodeRenderer()
    {
        if ($this->bbcodeRenderer !== null) {
            return $this->bbcodeRenderer;
        }

        $this->bbcodeParser();

        return $this->bbcodeRenderer;
    }

    private function loadStandardPlugins(Configurator $configurator)
    {
        $configurator->plugins->load('Autoemail');
        $configurator->plugins->load('Autoimage');
        $configurator->plugins->load('Autolink');
        $configurator->plugins->load('Autovideo');
        $configurator->Emoji;
        $configurator->plugins->load('FancyPants');
        $configurator->MediaEmbed;
        $configurator->MediaEmbed->add('youtube');

        return $configurator;
    }
}
