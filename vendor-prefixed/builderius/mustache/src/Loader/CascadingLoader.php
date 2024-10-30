<?php

namespace Builderius\Mustache\Loader;

use Builderius\Mustache\Exception\UnknownTemplateException;
use Builderius\Mustache\Loader;
/**
 * A Mustache Template cascading loader implementation, which delegates to other
 * Loader instances.
 */
class CascadingLoader implements \Builderius\Mustache\Loader
{
    private $loaders;
    /**
     * Construct a CascadingLoader with an array of loaders.
     *
     *     $loader = new Loader_CascadingLoader(array(
     *         new Loader_InlineLoader(__FILE__, __COMPILER_HALT_OFFSET__),
     *         new Loader_FilesystemLoader(__DIR__.'/templates')
     *     ));
     *
     * @param Loader[] $loaders
     */
    public function __construct(array $loaders = array())
    {
        $this->loaders = array();
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }
    /**
     * Add a Loader instance.
     *
     * @param Loader $loader
     */
    public function addLoader(\Builderius\Mustache\Loader $loader)
    {
        $this->loaders[] = $loader;
    }
    /**
     * Load a Template by name.
     *
     * @throws UnknownTemplateException If a template file is not found
     *
     * @param string $name
     *
     * @return string Mustache Template source
     */
    public function load($name)
    {
        foreach ($this->loaders as $loader) {
            try {
                return $loader->load($name);
            } catch (\Builderius\Mustache\Exception\UnknownTemplateException $e) {
                // do nothing, check the next loader.
            }
        }
        throw new \Builderius\Mustache\Exception\UnknownTemplateException($name);
    }
}
