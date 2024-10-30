<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Autoloader;

use Builderius\ProxyManager\FileLocator\FileLocatorInterface;
use Builderius\ProxyManager\Inflector\ClassNameInflectorInterface;
/**
 * {@inheritDoc}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class Autoloader implements \Builderius\ProxyManager\Autoloader\AutoloaderInterface
{
    /**
     * @var \Builderius\ProxyManager\FileLocator\FileLocatorInterface
     */
    protected $fileLocator;
    /**
     * @var \Builderius\ProxyManager\Inflector\ClassNameInflectorInterface
     */
    protected $classNameInflector;
    /**
     * @param \Builderius\ProxyManager\FileLocator\FileLocatorInterface      $fileLocator
     * @param \Builderius\ProxyManager\Inflector\ClassNameInflectorInterface $classNameInflector
     */
    public function __construct(\Builderius\ProxyManager\FileLocator\FileLocatorInterface $fileLocator, \Builderius\ProxyManager\Inflector\ClassNameInflectorInterface $classNameInflector)
    {
        $this->fileLocator = $fileLocator;
        $this->classNameInflector = $classNameInflector;
    }
    /**
     * {@inheritDoc}
     */
    public function __invoke(string $className) : bool
    {
        if (\class_exists($className, \false) || !$this->classNameInflector->isProxyClassName($className)) {
            return \false;
        }
        $file = $this->fileLocator->getProxyFileName($className);
        if (!\file_exists($file)) {
            return \false;
        }
        /* @noinspection PhpIncludeInspection */
        /* @noinspection UsingInclusionOnceReturnValueInspection */
        return (bool) (require_once $file);
    }
}
