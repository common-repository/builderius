<?php

namespace Builderius\Mustache\Loader;

use Builderius\Mustache\Exception\RuntimeException;
use Builderius\Mustache\Exception\UnknownTemplateException;
use Builderius\Mustache\Loader;
/**
 * Mustache Template filesystem Loader implementation.
 *
 * A FilesystemLoader instance loads Mustache Template source from the filesystem by name:
 *
 *     $loader = new Loader_FilesystemLoader(dirname(__FILE__).'/views');
 *     $tpl = $loader->load('foo'); // equivalent to `file_get_contents(dirname(__FILE__).'/views/foo.mustache');
 *
 * This is probably the most useful Mustache Loader implementation. It can be used for partials and normal Templates:
 *
 *     $m = new Mustache(array(
 *          'loader'          => new Loader_FilesystemLoader(dirname(__FILE__).'/views'),
 *          'partials_loader' => new Loader_FilesystemLoader(dirname(__FILE__).'/views/partials'),
 *     ));
 */
class FilesystemLoader implements \Builderius\Mustache\Loader
{
    private $baseDir;
    private $extension = '.mustache';
    private $templates = array();
    /**
     * Mustache filesystem Loader constructor.
     *
     * Passing an $options array allows overriding certain Loader options during instantiation:
     *
     *     $options = array(
     *         // The filename extension used for Mustache templates. Defaults to '.mustache'
     *         'extension' => '.ms',
     *     );
     *
     * @throws RuntimeException if $baseDir does not exist
     *
     * @param string $baseDir Base directory containing Mustache template files
     * @param array  $options Array of Loader options (default: array())
     */
    public function __construct($baseDir, array $options = array())
    {
        $this->baseDir = $baseDir;
        if (\strpos($this->baseDir, '://') === \false) {
            $this->baseDir = \realpath($this->baseDir);
        }
        if ($this->shouldCheckPath() && !\is_dir($this->baseDir)) {
            throw new \Builderius\Mustache\Exception\RuntimeException(\sprintf('FilesystemLoader baseDir must be a directory: %s', $baseDir));
        }
        if (\array_key_exists('extension', $options)) {
            if (empty($options['extension'])) {
                $this->extension = '';
            } else {
                $this->extension = '.' . \ltrim($options['extension'], '.');
            }
        }
    }
    /**
     * Load a Template by name.
     *
     *     $loader = new Loader_FilesystemLoader(dirname(__FILE__).'/views');
     *     $loader->load('admin/dashboard'); // loads "./views/admin/dashboard.mustache";
     *
     * @param string $name
     *
     * @return string Mustache Template source
     */
    public function load($name)
    {
        if (!isset($this->templates[$name])) {
            $this->templates[$name] = $this->loadFile($name);
        }
        return $this->templates[$name];
    }
    /**
     * Helper function for loading a Mustache file by name.
     *
     * @throws UnknownTemplateException If a template file is not found
     *
     * @param string $name
     *
     * @return string Mustache Template source
     */
    protected function loadFile($name)
    {
        $fileName = $this->getFileName($name);
        if ($this->shouldCheckPath() && !\file_exists($fileName)) {
            throw new \Builderius\Mustache\Exception\UnknownTemplateException($name);
        }
        return \file_get_contents($fileName);
    }
    /**
     * Helper function for getting a Mustache template file name.
     *
     * @param string $name
     *
     * @return string Template file name
     */
    protected function getFileName($name)
    {
        $fileName = $this->baseDir . '/' . $name;
        if (\substr($fileName, 0 - \strlen($this->extension)) !== $this->extension) {
            $fileName .= $this->extension;
        }
        return $fileName;
    }
    /**
     * Only check if baseDir is a directory and requested templates are files if
     * baseDir is using the filesystem stream wrapper.
     *
     * @return bool Whether to check `is_dir` and `file_exists`
     */
    protected function shouldCheckPath()
    {
        return \strpos($this->baseDir, '://') === \false || \strpos($this->baseDir, 'file://') === 0;
    }
}