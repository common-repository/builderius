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

use Builderius\Symfony\Component\Templating\Storage\Storage;
use Builderius\Symfony\Component\Templating\TemplateReferenceInterface;
/**
 * ChainLoader is a loader that calls other loaders to load templates.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ChainLoader extends \Builderius\Symfony\Component\Templating\Loader\Loader
{
    protected $loaders = [];
    /**
     * @param LoaderInterface[] $loaders An array of loader instances
     */
    public function __construct(array $loaders = [])
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }
    /**
     * Adds a loader instance.
     */
    public function addLoader(\Builderius\Symfony\Component\Templating\Loader\LoaderInterface $loader)
    {
        $this->loaders[] = $loader;
    }
    /**
     * Loads a template.
     *
     * @return Storage|bool false if the template cannot be loaded, a Storage instance otherwise
     */
    public function load(\Builderius\Symfony\Component\Templating\TemplateReferenceInterface $template)
    {
        foreach ($this->loaders as $loader) {
            if (\false !== ($storage = $loader->load($template))) {
                return $storage;
            }
        }
        return \false;
    }
    /**
     * Returns true if the template is still fresh.
     *
     * @param int $time The last modification time of the cached template (timestamp)
     *
     * @return bool
     */
    public function isFresh(\Builderius\Symfony\Component\Templating\TemplateReferenceInterface $template, int $time)
    {
        foreach ($this->loaders as $loader) {
            return $loader->isFresh($template, $time);
        }
        return \false;
    }
}
