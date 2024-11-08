<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\Templating\Loader;

use Builderius\Symfony\Component\Templating\Storage\FileStorage;
use Builderius\Symfony\Component\Templating\Storage\Storage;
use Builderius\Symfony\Component\Templating\TemplateReferenceInterface;
/**
 * FilesystemLoader is a loader that read templates from the filesystem.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FilesystemLoader extends \Builderius\Symfony\Component\Templating\Loader\Loader
{
    protected $templatePathPatterns;
    /**
     * @param string|string[] $templatePathPatterns An array of path patterns to look for templates
     */
    public function __construct($templatePathPatterns)
    {
        $this->templatePathPatterns = (array) $templatePathPatterns;
    }
    /**
     * Loads a template.
     *
     * @return Storage|bool false if the template cannot be loaded, a Storage instance otherwise
     */
    public function load(\Builderius\Symfony\Component\Templating\TemplateReferenceInterface $template)
    {
        $file = $template->get('name');
        if (self::isAbsolutePath($file) && \is_file($file)) {
            return new \Builderius\Symfony\Component\Templating\Storage\FileStorage($file);
        }
        $replacements = [];
        foreach ($template->all() as $key => $value) {
            $replacements['%' . $key . '%'] = $value;
        }
        $fileFailures = [];
        foreach ($this->templatePathPatterns as $templatePathPattern) {
            if (\is_file($file = \strtr($templatePathPattern, $replacements)) && \is_readable($file)) {
                if (null !== $this->logger) {
                    $this->logger->debug('Loaded template file.', ['file' => $file]);
                }
                return new \Builderius\Symfony\Component\Templating\Storage\FileStorage($file);
            }
            if (null !== $this->logger) {
                $fileFailures[] = $file;
            }
        }
        // only log failures if no template could be loaded at all
        foreach ($fileFailures as $file) {
            if (null !== $this->logger) {
                $this->logger->debug('Failed loading template file.', ['file' => $file]);
            }
        }
        return \false;
    }
    /**
     * Returns true if the template is still fresh.
     *
     * @param int $time The last modification time of the cached template (timestamp)
     *
     * @return bool true if the template is still fresh, false otherwise
     */
    public function isFresh(\Builderius\Symfony\Component\Templating\TemplateReferenceInterface $template, int $time)
    {
        if (\false === ($storage = $this->load($template))) {
            return \false;
        }
        return \filemtime((string) $storage) < $time;
    }
    /**
     * Returns true if the file is an existing absolute path.
     *
     * @return bool true if the path exists and is absolute, false otherwise
     */
    protected static function isAbsolutePath(string $file)
    {
        if ('/' == $file[0] || '\\' == $file[0] || \strlen($file) > 3 && \ctype_alpha($file[0]) && ':' == $file[1] && ('\\' == $file[2] || '/' == $file[2]) || null !== \parse_url($file, \PHP_URL_SCHEME)) {
            return \true;
        }
        return \false;
    }
}
