<?php

namespace Builderius\Mustache;

use Builderius\Mustache\Exception\UnknownTemplateException;
/**
 * Mustache Template Loader interface.
 */
interface Loader
{
    /**
     * Load a Template by name.
     *
     * @throws UnknownTemplateException If a template file is not found
     *
     * @param string $name
     *
     * @return string|Source Mustache Template source
     */
    public function load($name);
}
