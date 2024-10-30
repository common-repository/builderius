<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\DependencyInjection\Loader\Configurator;

use Builderius\Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Builderius\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Builderius\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\Definition;
use Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Builderius\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Builderius\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Builderius\Symfony\Component\ExpressionLanguage\Expression;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ContainerConfigurator extends \Builderius\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator
{
    const FACTORY = 'container';
    private $container;
    private $loader;
    private $instanceof;
    private $path;
    private $file;
    private $anonymousCount = 0;
    public function __construct(\Builderius\Symfony\Component\DependencyInjection\ContainerBuilder $container, \Builderius\Symfony\Component\DependencyInjection\Loader\PhpFileLoader $loader, array &$instanceof, string $path, string $file)
    {
        $this->container = $container;
        $this->loader = $loader;
        $this->instanceof =& $instanceof;
        $this->path = $path;
        $this->file = $file;
    }
    public final function extension(string $namespace, array $config)
    {
        if (!$this->container->hasExtension($namespace)) {
            $extensions = \array_filter(\array_map(function (\Builderius\Symfony\Component\DependencyInjection\Extension\ExtensionInterface $ext) {
                return $ext->getAlias();
            }, $this->container->getExtensions()));
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('There is no extension able to load the configuration for "%s" (in "%s"). Looked for namespace "%s", found "%s".', $namespace, $this->file, $namespace, $extensions ? \implode('", "', $extensions) : 'none'));
        }
        $this->container->loadFromExtension($namespace, static::processValue($config));
    }
    public final function import(string $resource, string $type = null, $ignoreErrors = \false)
    {
        $this->loader->setCurrentDir(\dirname($this->path));
        $this->loader->import($resource, $type, $ignoreErrors, $this->file);
    }
    public final function parameters() : \Builderius\Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator
    {
        return new \Builderius\Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator($this->container);
    }
    public final function services() : \Builderius\Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator
    {
        return new \Builderius\Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator($this->container, $this->loader, $this->instanceof, $this->path, $this->anonymousCount);
    }
}
/**
 * Creates a service reference.
 */
function ref(string $id) : \Builderius\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator
{
    return new \Builderius\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator($id);
}
/**
 * Creates an inline service.
 */
function inline(string $class = null) : \Builderius\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator
{
    return new \Builderius\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator(new \Builderius\Symfony\Component\DependencyInjection\Definition($class));
}
/**
 * Creates a service locator.
 *
 * @param ReferenceConfigurator[] $values
 */
function service_locator(array $values) : \Builderius\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument
{
    return new \Builderius\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument(\Builderius\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator::processValue($values, \true));
}
/**
 * Creates a lazy iterator.
 *
 * @param ReferenceConfigurator[] $values
 */
function iterator(array $values) : \Builderius\Symfony\Component\DependencyInjection\Argument\IteratorArgument
{
    return new \Builderius\Symfony\Component\DependencyInjection\Argument\IteratorArgument(\Builderius\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator::processValue($values, \true));
}
/**
 * Creates a lazy iterator by tag name.
 */
function tagged_iterator(string $tag, string $indexAttribute = null, string $defaultIndexMethod = null, string $defaultPriorityMethod = null) : \Builderius\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument
{
    return new \Builderius\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument($tag, $indexAttribute, $defaultIndexMethod, \false, $defaultPriorityMethod);
}
/**
 * Creates a service locator by tag name.
 */
function tagged_locator(string $tag, string $indexAttribute = null, string $defaultIndexMethod = null) : \Builderius\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument
{
    return new \Builderius\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument(new \Builderius\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument($tag, $indexAttribute, $defaultIndexMethod, \true));
}
/**
 * Creates an expression.
 */
function expr(string $expression) : \Builderius\Symfony\Component\ExpressionLanguage\Expression
{
    return new \Builderius\Symfony\Component\ExpressionLanguage\Expression($expression);
}
