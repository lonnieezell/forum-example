<?php

namespace Config;

use Michalsn\CodeIgniterHtmx\Config\Htmx as BaseHtmx;

class Htmx extends BaseHtmx
{
    /**
     * Enable / disable ToolbarDecorator.
     */
    public bool $toolbarDecorator = true;

    /**
     * Enable / diable ErrorModalDecorator.
     */
    public bool $errorModalDecorator = true;

    /**
     * The appearance of this string in the view
     * content will skip the htmx decorators. Even
     * when they are enabled.
     */
    public string $skipViewDecoratorsString = 'htmxSkipViewDecorators';

    /**
     * Enable / disable storing previous URLs for HTMX requests.
     */
    public bool $storePreviousURL = true;
}
