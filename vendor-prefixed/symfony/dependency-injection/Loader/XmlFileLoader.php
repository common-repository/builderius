<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\DependencyInjection\Loader;

use Builderius\Symfony\Component\Config\Util\XmlUtils;
use Builderius\Symfony\Component\DependencyInjection\Alias;
use Builderius\Symfony\Component\DependencyInjection\Argument\BoundArgument;
use Builderius\Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Builderius\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Builderius\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Builderius\Symfony\Component\DependencyInjection\ChildDefinition;
use Builderius\Symfony\Component\DependencyInjection\ContainerBuilder;
use Builderius\Symfony\Component\DependencyInjection\ContainerInterface;
use Builderius\Symfony\Component\DependencyInjection\Definition;
use Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Builderius\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Builderius\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Builderius\Symfony\Component\DependencyInjection\Reference;
use Builderius\Symfony\Component\ExpressionLanguage\Expression;
/**
 * XmlFileLoader loads XML files service definitions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class XmlFileLoader extends \Builderius\Symfony\Component\DependencyInjection\Loader\FileLoader
{
    const NS = 'http://symfony.com/schema/dic/services';
    protected $autoRegisterAliasesForSinglyImplementedInterfaces = \false;
    /**
     * {@inheritdoc}
     */
    public function load($resource, string $type = null)
    {
        $path = $this->locator->locate($resource);
        $xml = $this->parseFileToDOM($path);
        $this->container->fileExists($path);
        $defaults = $this->getServiceDefaults($xml, $path);
        // anonymous services
        $this->processAnonymousServices($xml, $path);
        // imports
        $this->parseImports($xml, $path);
        // parameters
        $this->parseParameters($xml, $path);
        // extensions
        $this->loadFromExtensions($xml);
        // services
        try {
            $this->parseDefinitions($xml, $path, $defaults);
        } finally {
            $this->instanceof = [];
            $this->registerAliasesForSinglyImplementedInterfaces();
        }
    }
    /**
     * {@inheritdoc}
     */
    public function supports($resource, string $type = null)
    {
        if (!\is_string($resource)) {
            return \false;
        }
        if (null === $type && 'xml' === \pathinfo($resource, \PATHINFO_EXTENSION)) {
            return \true;
        }
        return 'xml' === $type;
    }
    private function parseParameters(\DOMDocument $xml, string $file)
    {
        if ($parameters = $this->getChildren($xml->documentElement, 'parameters')) {
            $this->container->getParameterBag()->add($this->getArgumentsAsPhp($parameters[0], 'parameter', $file));
        }
    }
    private function parseImports(\DOMDocument $xml, string $file)
    {
        $xpath = new \DOMXPath($xml);
        $xpath->registerNamespace('container', self::NS);
        if (\false === ($imports = $xpath->query('//container:imports/container:import'))) {
            return;
        }
        $defaultDirectory = \dirname($file);
        foreach ($imports as $import) {
            $this->setCurrentDir($defaultDirectory);
            $this->import($import->getAttribute('resource'), \Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($import->getAttribute('type')) ?: null, \Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($import->getAttribute('ignore-errors')) ?: \false, $file);
        }
    }
    private function parseDefinitions(\DOMDocument $xml, string $file, array $defaults)
    {
        $xpath = new \DOMXPath($xml);
        $xpath->registerNamespace('container', self::NS);
        if (\false === ($services = $xpath->query('//container:services/container:service|//container:services/container:prototype'))) {
            return;
        }
        $this->setCurrentDir(\dirname($file));
        $this->instanceof = [];
        $this->isLoadingInstanceof = \true;
        $instanceof = $xpath->query('//container:services/container:instanceof');
        foreach ($instanceof as $service) {
            $this->setDefinition((string) $service->getAttribute('id'), $this->parseDefinition($service, $file, []));
        }
        $this->isLoadingInstanceof = \false;
        foreach ($services as $service) {
            if (null !== ($definition = $this->parseDefinition($service, $file, $defaults))) {
                if ('prototype' === $service->tagName) {
                    $excludes = \array_column($this->getChildren($service, 'exclude'), 'nodeValue');
                    if ($service->hasAttribute('exclude')) {
                        if (\count($excludes) > 0) {
                            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException('You cannot use both the attribute "exclude" and <exclude> tags at the same time.');
                        }
                        $excludes = [$service->getAttribute('exclude')];
                    }
                    $this->registerClasses($definition, (string) $service->getAttribute('namespace'), (string) $service->getAttribute('resource'), $excludes);
                } else {
                    $this->setDefinition((string) $service->getAttribute('id'), $definition);
                }
            }
        }
    }
    /**
     * Get service defaults.
     */
    private function getServiceDefaults(\DOMDocument $xml, string $file) : array
    {
        $xpath = new \DOMXPath($xml);
        $xpath->registerNamespace('container', self::NS);
        if (null === ($defaultsNode = $xpath->query('//container:services/container:defaults')->item(0))) {
            return [];
        }
        $bindings = [];
        foreach ($this->getArgumentsAsPhp($defaultsNode, 'bind', $file) as $argument => $value) {
            $bindings[$argument] = new \Builderius\Symfony\Component\DependencyInjection\Argument\BoundArgument($value, \true, \Builderius\Symfony\Component\DependencyInjection\Argument\BoundArgument::DEFAULTS_BINDING, $file);
        }
        $defaults = ['tags' => $this->getChildren($defaultsNode, 'tag'), 'bind' => $bindings];
        foreach ($defaults['tags'] as $tag) {
            if ('' === $tag->getAttribute('name')) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The tag name for tag "<defaults>" in "%s" must be a non-empty string.', $file));
            }
        }
        if ($defaultsNode->hasAttribute('autowire')) {
            $defaults['autowire'] = \Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($defaultsNode->getAttribute('autowire'));
        }
        if ($defaultsNode->hasAttribute('public')) {
            $defaults['public'] = \Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($defaultsNode->getAttribute('public'));
        }
        if ($defaultsNode->hasAttribute('autoconfigure')) {
            $defaults['autoconfigure'] = \Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($defaultsNode->getAttribute('autoconfigure'));
        }
        return $defaults;
    }
    /**
     * Parses an individual Definition.
     */
    private function parseDefinition(\DOMElement $service, string $file, array $defaults) : ?\Builderius\Symfony\Component\DependencyInjection\Definition
    {
        if ($alias = $service->getAttribute('alias')) {
            $this->validateAlias($service, $file);
            $this->container->setAlias((string) $service->getAttribute('id'), $alias = new \Builderius\Symfony\Component\DependencyInjection\Alias($alias));
            if ($publicAttr = $service->getAttribute('public')) {
                $alias->setPublic(\Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($publicAttr));
            } elseif (isset($defaults['public'])) {
                $alias->setPublic($defaults['public']);
            }
            if ($deprecated = $this->getChildren($service, 'deprecated')) {
                $alias->setDeprecated(\true, $deprecated[0]->nodeValue ?: null);
            }
            return null;
        }
        if ($this->isLoadingInstanceof) {
            $definition = new \Builderius\Symfony\Component\DependencyInjection\ChildDefinition('');
        } elseif ($parent = $service->getAttribute('parent')) {
            if (!empty($this->instanceof)) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The service "%s" cannot use the "parent" option in the same file where "instanceof" configuration is defined as using both is not supported. Move your child definitions to a separate file.', $service->getAttribute('id')));
            }
            foreach ($defaults as $k => $v) {
                if ('tags' === $k) {
                    // since tags are never inherited from parents, there is no confusion
                    // thus we can safely add them as defaults to ChildDefinition
                    continue;
                }
                if ('bind' === $k) {
                    if ($defaults['bind']) {
                        throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Bound values on service "%s" cannot be inherited from "defaults" when a "parent" is set. Move your child definitions to a separate file.', $service->getAttribute('id')));
                    }
                    continue;
                }
                if (!$service->hasAttribute($k)) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Attribute "%s" on service "%s" cannot be inherited from "defaults" when a "parent" is set. Move your child definitions to a separate file or define this attribute explicitly.', $k, $service->getAttribute('id')));
                }
            }
            $definition = new \Builderius\Symfony\Component\DependencyInjection\ChildDefinition($parent);
        } else {
            $definition = new \Builderius\Symfony\Component\DependencyInjection\Definition();
            if (isset($defaults['public'])) {
                $definition->setPublic($defaults['public']);
            }
            if (isset($defaults['autowire'])) {
                $definition->setAutowired($defaults['autowire']);
            }
            if (isset($defaults['autoconfigure'])) {
                $definition->setAutoconfigured($defaults['autoconfigure']);
            }
            $definition->setChanges([]);
        }
        foreach (['class', 'public', 'shared', 'synthetic', 'abstract'] as $key) {
            if ($value = $service->getAttribute($key)) {
                $method = 'set' . $key;
                $definition->{$method}($value = \Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($value));
            }
        }
        if ($value = $service->getAttribute('lazy')) {
            $definition->setLazy((bool) ($value = \Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($value)));
            if (\is_string($value)) {
                $definition->addTag('proxy', ['interface' => $value]);
            }
        }
        if ($value = $service->getAttribute('autowire')) {
            $definition->setAutowired(\Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($value));
        }
        if ($value = $service->getAttribute('autoconfigure')) {
            if (!$definition instanceof \Builderius\Symfony\Component\DependencyInjection\ChildDefinition) {
                $definition->setAutoconfigured(\Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($value));
            } elseif ($value = \Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($value)) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The service "%s" cannot have a "parent" and also have "autoconfigure". Try setting autoconfigure="false" for the service.', $service->getAttribute('id')));
            }
        }
        if ($files = $this->getChildren($service, 'file')) {
            $definition->setFile($files[0]->nodeValue);
        }
        if ($deprecated = $this->getChildren($service, 'deprecated')) {
            $definition->setDeprecated(\true, $deprecated[0]->nodeValue ?: null);
        }
        $definition->setArguments($this->getArgumentsAsPhp($service, 'argument', $file, $definition instanceof \Builderius\Symfony\Component\DependencyInjection\ChildDefinition));
        $definition->setProperties($this->getArgumentsAsPhp($service, 'property', $file));
        if ($factories = $this->getChildren($service, 'factory')) {
            $factory = $factories[0];
            if ($function = $factory->getAttribute('function')) {
                $definition->setFactory($function);
            } else {
                if ($childService = $factory->getAttribute('service')) {
                    $class = new \Builderius\Symfony\Component\DependencyInjection\Reference($childService, \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE);
                } else {
                    $class = $factory->hasAttribute('class') ? $factory->getAttribute('class') : null;
                }
                $definition->setFactory([$class, $factory->getAttribute('method') ?: '__invoke']);
            }
        }
        if ($configurators = $this->getChildren($service, 'configurator')) {
            $configurator = $configurators[0];
            if ($function = $configurator->getAttribute('function')) {
                $definition->setConfigurator($function);
            } else {
                if ($childService = $configurator->getAttribute('service')) {
                    $class = new \Builderius\Symfony\Component\DependencyInjection\Reference($childService, \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE);
                } else {
                    $class = $configurator->getAttribute('class');
                }
                $definition->setConfigurator([$class, $configurator->getAttribute('method') ?: '__invoke']);
            }
        }
        foreach ($this->getChildren($service, 'call') as $call) {
            $definition->addMethodCall($call->getAttribute('method'), $this->getArgumentsAsPhp($call, 'argument', $file), \Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($call->getAttribute('returns-clone')));
        }
        $tags = $this->getChildren($service, 'tag');
        if (!empty($defaults['tags'])) {
            $tags = \array_merge($tags, $defaults['tags']);
        }
        foreach ($tags as $tag) {
            $parameters = [];
            foreach ($tag->attributes as $name => $node) {
                if ('name' === $name) {
                    continue;
                }
                if (\false !== \strpos($name, '-') && \false === \strpos($name, '_') && !\array_key_exists($normalizedName = \str_replace('-', '_', $name), $parameters)) {
                    $parameters[$normalizedName] = \Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($node->nodeValue);
                }
                // keep not normalized key
                $parameters[$name] = \Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($node->nodeValue);
            }
            if ('' === $tag->getAttribute('name')) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The tag name for service "%s" in "%s" must be a non-empty string.', (string) $service->getAttribute('id'), $file));
            }
            $definition->addTag($tag->getAttribute('name'), $parameters);
        }
        $bindings = $this->getArgumentsAsPhp($service, 'bind', $file);
        $bindingType = $this->isLoadingInstanceof ? \Builderius\Symfony\Component\DependencyInjection\Argument\BoundArgument::INSTANCEOF_BINDING : \Builderius\Symfony\Component\DependencyInjection\Argument\BoundArgument::SERVICE_BINDING;
        foreach ($bindings as $argument => $value) {
            $bindings[$argument] = new \Builderius\Symfony\Component\DependencyInjection\Argument\BoundArgument($value, \true, $bindingType, $file);
        }
        if (isset($defaults['bind'])) {
            // deep clone, to avoid multiple process of the same instance in the passes
            $bindings = \array_merge(\unserialize(\serialize($defaults['bind'])), $bindings);
        }
        if ($bindings) {
            $definition->setBindings($bindings);
        }
        if ($decorates = $service->getAttribute('decorates')) {
            $decorationOnInvalid = $service->getAttribute('decoration-on-invalid') ?: 'exception';
            if ('exception' === $decorationOnInvalid) {
                $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
            } elseif ('ignore' === $decorationOnInvalid) {
                $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE;
            } elseif ('null' === $decorationOnInvalid) {
                $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::NULL_ON_INVALID_REFERENCE;
            } else {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid value "%s" for attribute "decoration-on-invalid" on service "%s". Did you mean "exception", "ignore" or "null" in "%s"?', $decorationOnInvalid, (string) $service->getAttribute('id'), $file));
            }
            $renameId = $service->hasAttribute('decoration-inner-name') ? $service->getAttribute('decoration-inner-name') : null;
            $priority = $service->hasAttribute('decoration-priority') ? $service->getAttribute('decoration-priority') : 0;
            $definition->setDecoratedService($decorates, $renameId, $priority, $invalidBehavior);
        }
        return $definition;
    }
    /**
     * Parses a XML file to a \DOMDocument.
     *
     * @throws InvalidArgumentException When loading of XML file returns error
     */
    private function parseFileToDOM(string $file) : \DOMDocument
    {
        try {
            $dom = \Builderius\Symfony\Component\Config\Util\XmlUtils::loadFile($file, [$this, 'validateSchema']);
        } catch (\InvalidArgumentException $e) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Unable to parse file "%s": ', $file) . $e->getMessage(), $e->getCode(), $e);
        }
        $this->validateExtensions($dom, $file);
        return $dom;
    }
    /**
     * Processes anonymous services.
     */
    private function processAnonymousServices(\DOMDocument $xml, string $file)
    {
        $definitions = [];
        $count = 0;
        $suffix = '~' . \Builderius\Symfony\Component\DependencyInjection\ContainerBuilder::hash($file);
        $xpath = new \DOMXPath($xml);
        $xpath->registerNamespace('container', self::NS);
        // anonymous services as arguments/properties
        if (\false !== ($nodes = $xpath->query('//container:argument[@type="service"][not(@id)]|//container:property[@type="service"][not(@id)]|//container:bind[not(@id)]|//container:factory[not(@service)]|//container:configurator[not(@service)]'))) {
            foreach ($nodes as $node) {
                if ($services = $this->getChildren($node, 'service')) {
                    // give it a unique name
                    $id = \sprintf('.%d_%s', ++$count, \preg_replace('/^.*\\\\/', '', $services[0]->getAttribute('class')) . $suffix);
                    $node->setAttribute('id', $id);
                    $node->setAttribute('service', $id);
                    $definitions[$id] = [$services[0], $file];
                    $services[0]->setAttribute('id', $id);
                    // anonymous services are always private
                    // we could not use the constant false here, because of XML parsing
                    $services[0]->setAttribute('public', 'false');
                }
            }
        }
        // anonymous services "in the wild"
        if (\false !== ($nodes = $xpath->query('//container:services/container:service[not(@id)]'))) {
            foreach ($nodes as $node) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Top-level services must have "id" attribute, none found in "%s" at line %d.', $file, $node->getLineNo()));
            }
        }
        // resolve definitions
        \uksort($definitions, 'strnatcmp');
        foreach (\array_reverse($definitions) as $id => list($domElement, $file)) {
            if (null !== ($definition = $this->parseDefinition($domElement, $file, []))) {
                $this->setDefinition($id, $definition);
            }
        }
    }
    private function getArgumentsAsPhp(\DOMElement $node, string $name, string $file, bool $isChildDefinition = \false) : array
    {
        $arguments = [];
        foreach ($this->getChildren($node, $name) as $arg) {
            if ($arg->hasAttribute('name')) {
                $arg->setAttribute('key', $arg->getAttribute('name'));
            }
            // this is used by ChildDefinition to overwrite a specific
            // argument of the parent definition
            if ($arg->hasAttribute('index')) {
                $key = ($isChildDefinition ? 'index_' : '') . $arg->getAttribute('index');
            } elseif (!$arg->hasAttribute('key')) {
                // Append an empty argument, then fetch its key to overwrite it later
                $arguments[] = null;
                $keys = \array_keys($arguments);
                $key = \array_pop($keys);
            } else {
                $key = $arg->getAttribute('key');
            }
            $onInvalid = $arg->getAttribute('on-invalid');
            $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
            if ('ignore' == $onInvalid) {
                $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE;
            } elseif ('ignore_uninitialized' == $onInvalid) {
                $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE;
            } elseif ('null' == $onInvalid) {
                $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::NULL_ON_INVALID_REFERENCE;
            }
            switch ($arg->getAttribute('type')) {
                case 'service':
                    if ('' === $arg->getAttribute('id')) {
                        throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Tag "<%s>" with type="service" has no or empty "id" attribute in "%s".', $name, $file));
                    }
                    $arguments[$key] = new \Builderius\Symfony\Component\DependencyInjection\Reference($arg->getAttribute('id'), $invalidBehavior);
                    break;
                case 'expression':
                    if (!\class_exists(\Builderius\Symfony\Component\ExpressionLanguage\Expression::class)) {
                        throw new \LogicException(\sprintf('The type="expression" attribute cannot be used without the ExpressionLanguage component. Try running "composer require symfony/expression-language".'));
                    }
                    $arguments[$key] = new \Builderius\Symfony\Component\ExpressionLanguage\Expression($arg->nodeValue);
                    break;
                case 'collection':
                    $arguments[$key] = $this->getArgumentsAsPhp($arg, $name, $file);
                    break;
                case 'iterator':
                    $arg = $this->getArgumentsAsPhp($arg, $name, $file);
                    try {
                        $arguments[$key] = new \Builderius\Symfony\Component\DependencyInjection\Argument\IteratorArgument($arg);
                    } catch (\Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException $e) {
                        throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Tag "<%s>" with type="iterator" only accepts collections of type="service" references in "%s".', $name, $file));
                    }
                    break;
                case 'service_locator':
                    $arg = $this->getArgumentsAsPhp($arg, $name, $file);
                    try {
                        $arguments[$key] = new \Builderius\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument($arg);
                    } catch (\Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException $e) {
                        throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Tag "<%s>" with type="service_locator" only accepts maps of type="service" references in "%s".', $name, $file));
                    }
                    break;
                case 'tagged':
                case 'tagged_iterator':
                case 'tagged_locator':
                    $type = $arg->getAttribute('type');
                    $forLocator = 'tagged_locator' === $type;
                    if (!$arg->getAttribute('tag')) {
                        throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Tag "<%s>" with type="%s" has no or empty "tag" attribute in "%s".', $name, $type, $file));
                    }
                    $arguments[$key] = new \Builderius\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument($arg->getAttribute('tag'), $arg->getAttribute('index-by') ?: null, $arg->getAttribute('default-index-method') ?: null, $forLocator, $arg->getAttribute('default-priority-method') ?: null);
                    if ($forLocator) {
                        $arguments[$key] = new \Builderius\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument($arguments[$key]);
                    }
                    break;
                case 'binary':
                    if (\false === ($value = \base64_decode($arg->nodeValue))) {
                        throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Tag "<%s>" with type="binary" is not a valid base64 encoded string.', $name));
                    }
                    $arguments[$key] = $value;
                    break;
                case 'string':
                    $arguments[$key] = $arg->nodeValue;
                    break;
                case 'constant':
                    $arguments[$key] = \constant(\trim($arg->nodeValue));
                    break;
                default:
                    $arguments[$key] = \Builderius\Symfony\Component\Config\Util\XmlUtils::phpize($arg->nodeValue);
            }
        }
        return $arguments;
    }
    /**
     * Get child elements by name.
     *
     * @return \DOMElement[]
     */
    private function getChildren(\DOMNode $node, string $name) : array
    {
        $children = [];
        foreach ($node->childNodes as $child) {
            if ($child instanceof \DOMElement && $child->localName === $name && self::NS === $child->namespaceURI) {
                $children[] = $child;
            }
        }
        return $children;
    }
    /**
     * Validates a documents XML schema.
     *
     * @return bool
     *
     * @throws RuntimeException When extension references a non-existent XSD file
     */
    public function validateSchema(\DOMDocument $dom)
    {
        $schemaLocations = ['http://symfony.com/schema/dic/services' => \str_replace('\\', '/', __DIR__ . '/schema/dic/services/services-1.0.xsd')];
        if ($element = $dom->documentElement->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation')) {
            $items = \preg_split('/\\s+/', $element);
            for ($i = 0, $nb = \count($items); $i < $nb; $i += 2) {
                if (!$this->container->hasExtension($items[$i])) {
                    continue;
                }
                if (($extension = $this->container->getExtension($items[$i])) && \false !== $extension->getXsdValidationBasePath()) {
                    $ns = $extension->getNamespace();
                    $path = \str_replace([$ns, \str_replace('http://', 'https://', $ns)], \str_replace('\\', '/', $extension->getXsdValidationBasePath()) . '/', $items[$i + 1]);
                    if (!\is_file($path)) {
                        throw new \Builderius\Symfony\Component\DependencyInjection\Exception\RuntimeException(\sprintf('Extension "%s" references a non-existent XSD file "%s".', \get_class($extension), $path));
                    }
                    $schemaLocations[$items[$i]] = $path;
                }
            }
        }
        $tmpfiles = [];
        $imports = '';
        foreach ($schemaLocations as $namespace => $location) {
            $parts = \explode('/', $location);
            $locationstart = 'file:///';
            if (0 === \stripos($location, 'phar://')) {
                $tmpfile = \tempnam(\sys_get_temp_dir(), 'symfony');
                if ($tmpfile) {
                    \copy($location, $tmpfile);
                    $tmpfiles[] = $tmpfile;
                    $parts = \explode('/', \str_replace('\\', '/', $tmpfile));
                } else {
                    \array_shift($parts);
                    $locationstart = 'phar:///';
                }
            }
            $drive = '\\' === \DIRECTORY_SEPARATOR ? \array_shift($parts) . '/' : '';
            $location = $locationstart . $drive . \implode('/', \array_map('rawurlencode', $parts));
            $imports .= \sprintf('  <xsd:import namespace="%s" schemaLocation="%s" />' . "\n", $namespace, $location);
        }
        $source = <<<EOF
<?xml version="1.0" encoding="utf-8" ?>
<xsd:schema xmlns="http://symfony.com/schema"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    targetNamespace="http://symfony.com/schema"
    elementFormDefault="qualified">

    <xsd:import namespace="http://www.w3.org/XML/1998/namespace"/>
{$imports}
</xsd:schema>
EOF;
        $disableEntities = \libxml_disable_entity_loader(\false);
        $valid = @$dom->schemaValidateSource($source);
        \libxml_disable_entity_loader($disableEntities);
        foreach ($tmpfiles as $tmpfile) {
            @\unlink($tmpfile);
        }
        return $valid;
    }
    private function validateAlias(\DOMElement $alias, string $file)
    {
        foreach ($alias->attributes as $name => $node) {
            if (!\in_array($name, ['alias', 'id', 'public'])) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid attribute "%s" defined for alias "%s" in "%s".', $name, $alias->getAttribute('id'), $file));
            }
        }
        foreach ($alias->childNodes as $child) {
            if (!$child instanceof \DOMElement || self::NS !== $child->namespaceURI) {
                continue;
            }
            if (!\in_array($child->localName, ['deprecated'], \true)) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid child element "%s" defined for alias "%s" in "%s".', $child->localName, $alias->getAttribute('id'), $file));
            }
        }
    }
    /**
     * Validates an extension.
     *
     * @throws InvalidArgumentException When no extension is found corresponding to a tag
     */
    private function validateExtensions(\DOMDocument $dom, string $file)
    {
        foreach ($dom->documentElement->childNodes as $node) {
            if (!$node instanceof \DOMElement || 'http://symfony.com/schema/dic/services' === $node->namespaceURI) {
                continue;
            }
            // can it be handled by an extension?
            if (!$this->container->hasExtension($node->namespaceURI)) {
                $extensionNamespaces = \array_filter(\array_map(function (\Builderius\Symfony\Component\DependencyInjection\Extension\ExtensionInterface $ext) {
                    return $ext->getNamespace();
                }, $this->container->getExtensions()));
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('There is no extension able to load the configuration for "%s" (in "%s"). Looked for namespace "%s", found "%s".', $node->tagName, $file, $node->namespaceURI, $extensionNamespaces ? \implode('", "', $extensionNamespaces) : 'none'));
            }
        }
    }
    /**
     * Loads from an extension.
     */
    private function loadFromExtensions(\DOMDocument $xml)
    {
        foreach ($xml->documentElement->childNodes as $node) {
            if (!$node instanceof \DOMElement || self::NS === $node->namespaceURI) {
                continue;
            }
            $values = static::convertDomElementToArray($node);
            if (!\is_array($values)) {
                $values = [];
            }
            $this->container->loadFromExtension($node->namespaceURI, $values);
        }
    }
    /**
     * Converts a \DOMElement object to a PHP array.
     *
     * The following rules applies during the conversion:
     *
     *  * Each tag is converted to a key value or an array
     *    if there is more than one "value"
     *
     *  * The content of a tag is set under a "value" key (<foo>bar</foo>)
     *    if the tag also has some nested tags
     *
     *  * The attributes are converted to keys (<foo foo="bar"/>)
     *
     *  * The nested-tags are converted to keys (<foo><foo>bar</foo></foo>)
     *
     * @param \DOMElement $element A \DOMElement instance
     *
     * @return mixed
     */
    public static function convertDomElementToArray(\DOMElement $element)
    {
        return \Builderius\Symfony\Component\Config\Util\XmlUtils::convertDomElementToArray($element);
    }
}
