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

use Builderius\Symfony\Component\DependencyInjection\Alias;
use Builderius\Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
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
use Builderius\Symfony\Component\Yaml\Exception\ParseException;
use Builderius\Symfony\Component\Yaml\Parser as YamlParser;
use Builderius\Symfony\Component\Yaml\Tag\TaggedValue;
use Builderius\Symfony\Component\Yaml\Yaml;
/**
 * YamlFileLoader loads YAML files service definitions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class YamlFileLoader extends \Builderius\Symfony\Component\DependencyInjection\Loader\FileLoader
{
    private static $serviceKeywords = ['alias' => 'alias', 'parent' => 'parent', 'class' => 'class', 'shared' => 'shared', 'synthetic' => 'synthetic', 'lazy' => 'lazy', 'public' => 'public', 'abstract' => 'abstract', 'deprecated' => 'deprecated', 'factory' => 'factory', 'file' => 'file', 'arguments' => 'arguments', 'properties' => 'properties', 'configurator' => 'configurator', 'calls' => 'calls', 'tags' => 'tags', 'decorates' => 'decorates', 'decoration_inner_name' => 'decoration_inner_name', 'decoration_priority' => 'decoration_priority', 'decoration_on_invalid' => 'decoration_on_invalid', 'autowire' => 'autowire', 'autoconfigure' => 'autoconfigure', 'bind' => 'bind'];
    private static $prototypeKeywords = ['resource' => 'resource', 'namespace' => 'namespace', 'exclude' => 'exclude', 'parent' => 'parent', 'shared' => 'shared', 'lazy' => 'lazy', 'public' => 'public', 'abstract' => 'abstract', 'deprecated' => 'deprecated', 'factory' => 'factory', 'arguments' => 'arguments', 'properties' => 'properties', 'configurator' => 'configurator', 'calls' => 'calls', 'tags' => 'tags', 'autowire' => 'autowire', 'autoconfigure' => 'autoconfigure', 'bind' => 'bind'];
    private static $instanceofKeywords = ['shared' => 'shared', 'lazy' => 'lazy', 'public' => 'public', 'properties' => 'properties', 'configurator' => 'configurator', 'calls' => 'calls', 'tags' => 'tags', 'autowire' => 'autowire', 'bind' => 'bind'];
    private static $defaultsKeywords = ['public' => 'public', 'tags' => 'tags', 'autowire' => 'autowire', 'autoconfigure' => 'autoconfigure', 'bind' => 'bind'];
    private $yamlParser;
    private $anonymousServicesCount;
    private $anonymousServicesSuffix;
    protected $autoRegisterAliasesForSinglyImplementedInterfaces = \false;
    /**
     * {@inheritdoc}
     */
    public function load($resource, string $type = null)
    {
        $path = $this->locator->locate($resource);
        $content = $this->loadFile($path);
        $this->container->fileExists($path);
        // empty file
        if (null === $content) {
            return;
        }
        // imports
        $this->parseImports($content, $path);
        // parameters
        if (isset($content['parameters'])) {
            if (!\is_array($content['parameters'])) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The "parameters" key should contain an array in "%s". Check your YAML syntax.', $path));
            }
            foreach ($content['parameters'] as $key => $value) {
                $this->container->setParameter($key, $this->resolveServices($value, $path, \true));
            }
        }
        // extensions
        $this->loadFromExtensions($content);
        // services
        $this->anonymousServicesCount = 0;
        $this->anonymousServicesSuffix = '~' . \Builderius\Symfony\Component\DependencyInjection\ContainerBuilder::hash($path);
        $this->setCurrentDir(\dirname($path));
        try {
            $this->parseDefinitions($content, $path);
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
        if (null === $type && \in_array(\pathinfo($resource, \PATHINFO_EXTENSION), ['yaml', 'yml'], \true)) {
            return \true;
        }
        return \in_array($type, ['yaml', 'yml'], \true);
    }
    private function parseImports(array $content, string $file)
    {
        if (!isset($content['imports'])) {
            return;
        }
        if (!\is_array($content['imports'])) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The "imports" key should contain an array in "%s". Check your YAML syntax.', $file));
        }
        $defaultDirectory = \dirname($file);
        foreach ($content['imports'] as $import) {
            if (!\is_array($import)) {
                $import = ['resource' => $import];
            }
            if (!isset($import['resource'])) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('An import should provide a resource in "%s". Check your YAML syntax.', $file));
            }
            $this->setCurrentDir($defaultDirectory);
            $this->import($import['resource'], $import['type'] ?? null, $import['ignore_errors'] ?? \false, $file);
        }
    }
    private function parseDefinitions(array $content, string $file)
    {
        if (!isset($content['services'])) {
            return;
        }
        if (!\is_array($content['services'])) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The "services" key should contain an array in "%s". Check your YAML syntax.', $file));
        }
        if (\array_key_exists('_instanceof', $content['services'])) {
            $instanceof = $content['services']['_instanceof'];
            unset($content['services']['_instanceof']);
            if (!\is_array($instanceof)) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Service "_instanceof" key must be an array, "%s" given in "%s".', \gettype($instanceof), $file));
            }
            $this->instanceof = [];
            $this->isLoadingInstanceof = \true;
            foreach ($instanceof as $id => $service) {
                if (!$service || !\is_array($service)) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Type definition "%s" must be a non-empty array within "_instanceof" in "%s". Check your YAML syntax.', $id, $file));
                }
                if (\is_string($service) && 0 === \strpos($service, '@')) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Type definition "%s" cannot be an alias within "_instanceof" in "%s". Check your YAML syntax.', $id, $file));
                }
                $this->parseDefinition($id, $service, $file, []);
            }
        }
        $this->isLoadingInstanceof = \false;
        $defaults = $this->parseDefaults($content, $file);
        foreach ($content['services'] as $id => $service) {
            $this->parseDefinition($id, $service, $file, $defaults);
        }
    }
    /**
     * @throws InvalidArgumentException
     */
    private function parseDefaults(array &$content, string $file) : array
    {
        if (!\array_key_exists('_defaults', $content['services'])) {
            return [];
        }
        $defaults = $content['services']['_defaults'];
        unset($content['services']['_defaults']);
        if (!\is_array($defaults)) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Service "_defaults" key must be an array, "%s" given in "%s".', \gettype($defaults), $file));
        }
        foreach ($defaults as $key => $default) {
            if (!isset(self::$defaultsKeywords[$key])) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The configuration key "%s" cannot be used to define a default value in "%s". Allowed keys are "%s".', $key, $file, \implode('", "', self::$defaultsKeywords)));
            }
        }
        if (isset($defaults['tags'])) {
            if (!\is_array($tags = $defaults['tags'])) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Parameter "tags" in "_defaults" must be an array in "%s". Check your YAML syntax.', $file));
            }
            foreach ($tags as $tag) {
                if (!\is_array($tag)) {
                    $tag = ['name' => $tag];
                }
                if (!isset($tag['name'])) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('A "tags" entry in "_defaults" is missing a "name" key in "%s".', $file));
                }
                $name = $tag['name'];
                unset($tag['name']);
                if (!\is_string($name) || '' === $name) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The tag name in "_defaults" must be a non-empty string in "%s".', $file));
                }
                foreach ($tag as $attribute => $value) {
                    if (!\is_scalar($value) && null !== $value) {
                        throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Tag "%s", attribute "%s" in "_defaults" must be of a scalar-type in "%s". Check your YAML syntax.', $name, $attribute, $file));
                    }
                }
            }
        }
        if (isset($defaults['bind'])) {
            if (!\is_array($defaults['bind'])) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Parameter "bind" in "_defaults" must be an array in "%s". Check your YAML syntax.', $file));
            }
            foreach ($this->resolveServices($defaults['bind'], $file) as $argument => $value) {
                $defaults['bind'][$argument] = new \Builderius\Symfony\Component\DependencyInjection\Argument\BoundArgument($value, \true, \Builderius\Symfony\Component\DependencyInjection\Argument\BoundArgument::DEFAULTS_BINDING, $file);
            }
        }
        return $defaults;
    }
    private function isUsingShortSyntax(array $service) : bool
    {
        foreach ($service as $key => $value) {
            if (\is_string($key) && ('' === $key || '$' !== $key[0] && \false === \strpos($key, '\\'))) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * Parses a definition.
     *
     * @param array|string $service
     *
     * @throws InvalidArgumentException When tags are invalid
     */
    private function parseDefinition(string $id, $service, string $file, array $defaults)
    {
        if (\preg_match('/^_[a-zA-Z0-9_]*$/', $id)) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Service names that start with an underscore are reserved. Rename the "%s" service or define it in XML instead.', $id));
        }
        if (\is_string($service) && 0 === \strpos($service, '@')) {
            $this->container->setAlias($id, $alias = new \Builderius\Symfony\Component\DependencyInjection\Alias(\substr($service, 1)));
            if (isset($defaults['public'])) {
                $alias->setPublic($defaults['public']);
            }
            return;
        }
        if (\is_array($service) && $this->isUsingShortSyntax($service)) {
            $service = ['arguments' => $service];
        }
        if (null === $service) {
            $service = [];
        }
        if (!\is_array($service)) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('A service definition must be an array or a string starting with "@" but "%s" found for service "%s" in "%s". Check your YAML syntax.', \gettype($service), $id, $file));
        }
        $this->checkDefinition($id, $service, $file);
        if (isset($service['alias'])) {
            $this->container->setAlias($id, $alias = new \Builderius\Symfony\Component\DependencyInjection\Alias($service['alias']));
            if (isset($service['public'])) {
                $alias->setPublic($service['public']);
            } elseif (isset($defaults['public'])) {
                $alias->setPublic($defaults['public']);
            }
            foreach ($service as $key => $value) {
                if (!\in_array($key, ['alias', 'public', 'deprecated'])) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The configuration key "%s" is unsupported for the service "%s" which is defined as an alias in "%s". Allowed configuration keys for service aliases are "alias", "public" and "deprecated".', $key, $id, $file));
                }
                if ('deprecated' === $key) {
                    $alias->setDeprecated(\true, $value);
                }
            }
            return;
        }
        if ($this->isLoadingInstanceof) {
            $definition = new \Builderius\Symfony\Component\DependencyInjection\ChildDefinition('');
        } elseif (isset($service['parent'])) {
            if (!empty($this->instanceof)) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The service "%s" cannot use the "parent" option in the same file where "_instanceof" configuration is defined as using both is not supported. Move your child definitions to a separate file.', $id));
            }
            foreach ($defaults as $k => $v) {
                if ('tags' === $k) {
                    // since tags are never inherited from parents, there is no confusion
                    // thus we can safely add them as defaults to ChildDefinition
                    continue;
                }
                if ('bind' === $k) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Attribute "bind" on service "%s" cannot be inherited from "_defaults" when a "parent" is set. Move your child definitions to a separate file.', $id));
                }
                if (!isset($service[$k])) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Attribute "%s" on service "%s" cannot be inherited from "_defaults" when a "parent" is set. Move your child definitions to a separate file or define this attribute explicitly.', $k, $id));
                }
            }
            if ('' !== $service['parent'] && '@' === $service['parent'][0]) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The value of the "parent" option for the "%s" service must be the id of the service without the "@" prefix (replace "%s" with "%s").', $id, $service['parent'], \substr($service['parent'], 1)));
            }
            $definition = new \Builderius\Symfony\Component\DependencyInjection\ChildDefinition($service['parent']);
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
        if (isset($service['class'])) {
            $definition->setClass($service['class']);
        }
        if (isset($service['shared'])) {
            $definition->setShared($service['shared']);
        }
        if (isset($service['synthetic'])) {
            $definition->setSynthetic($service['synthetic']);
        }
        if (isset($service['lazy'])) {
            $definition->setLazy((bool) $service['lazy']);
            if (\is_string($service['lazy'])) {
                $definition->addTag('proxy', ['interface' => $service['lazy']]);
            }
        }
        if (isset($service['public'])) {
            $definition->setPublic($service['public']);
        }
        if (isset($service['abstract'])) {
            $definition->setAbstract($service['abstract']);
        }
        if (\array_key_exists('deprecated', $service)) {
            $definition->setDeprecated(\true, $service['deprecated']);
        }
        if (isset($service['factory'])) {
            $definition->setFactory($this->parseCallable($service['factory'], 'factory', $id, $file));
        }
        if (isset($service['file'])) {
            $definition->setFile($service['file']);
        }
        if (isset($service['arguments'])) {
            $definition->setArguments($this->resolveServices($service['arguments'], $file));
        }
        if (isset($service['properties'])) {
            $definition->setProperties($this->resolveServices($service['properties'], $file));
        }
        if (isset($service['configurator'])) {
            $definition->setConfigurator($this->parseCallable($service['configurator'], 'configurator', $id, $file));
        }
        if (isset($service['calls'])) {
            if (!\is_array($service['calls'])) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Parameter "calls" must be an array for service "%s" in "%s". Check your YAML syntax.', $id, $file));
            }
            foreach ($service['calls'] as $k => $call) {
                if (!\is_array($call) && (!\is_string($k) || !$call instanceof \Builderius\Symfony\Component\Yaml\Tag\TaggedValue)) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid method call for service "%s": expected map or array, "%s" given in "%s".', $id, $call instanceof \Builderius\Symfony\Component\Yaml\Tag\TaggedValue ? '!' . $call->getTag() : \gettype($call), $file));
                }
                if (\is_string($k)) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid method call for service "%s", did you forgot a leading dash before "%s: ..." in "%s"?', $id, $k, $file));
                }
                if (isset($call['method'])) {
                    $method = $call['method'];
                    $args = $call['arguments'] ?? [];
                    $returnsClone = $call['returns_clone'] ?? \false;
                } else {
                    if (1 === \count($call) && \is_string(\key($call))) {
                        $method = \key($call);
                        $args = $call[$method];
                        if ($args instanceof \Builderius\Symfony\Component\Yaml\Tag\TaggedValue) {
                            if ('returns_clone' !== $args->getTag()) {
                                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Unsupported tag "!%s", did you mean "!returns_clone" for service "%s" in "%s"?', $args->getTag(), $id, $file));
                            }
                            $returnsClone = \true;
                            $args = $args->getValue();
                        } else {
                            $returnsClone = \false;
                        }
                    } elseif (empty($call[0])) {
                        throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid call for service "%s": the method must be defined as the first index of an array or as the only key of a map in "%s".', $id, $file));
                    } else {
                        $method = $call[0];
                        $args = $call[1] ?? [];
                        $returnsClone = $call[2] ?? \false;
                    }
                }
                if (!\is_array($args)) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The second parameter for function call "%s" must be an array of its arguments for service "%s" in "%s". Check your YAML syntax.', $method, $id, $file));
                }
                $args = $this->resolveServices($args, $file);
                $definition->addMethodCall($method, $args, $returnsClone);
            }
        }
        $tags = isset($service['tags']) ? $service['tags'] : [];
        if (!\is_array($tags)) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Parameter "tags" must be an array for service "%s" in "%s". Check your YAML syntax.', $id, $file));
        }
        if (isset($defaults['tags'])) {
            $tags = \array_merge($tags, $defaults['tags']);
        }
        foreach ($tags as $tag) {
            if (!\is_array($tag)) {
                $tag = ['name' => $tag];
            }
            if (!isset($tag['name'])) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('A "tags" entry is missing a "name" key for service "%s" in "%s".', $id, $file));
            }
            $name = $tag['name'];
            unset($tag['name']);
            if (!\is_string($name) || '' === $name) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The tag name for service "%s" in "%s" must be a non-empty string.', $id, $file));
            }
            foreach ($tag as $attribute => $value) {
                if (!\is_scalar($value) && null !== $value) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('A "tags" attribute must be of a scalar-type for service "%s", tag "%s", attribute "%s" in "%s". Check your YAML syntax.', $id, $name, $attribute, $file));
                }
            }
            $definition->addTag($name, $tag);
        }
        if (null !== ($decorates = $service['decorates'] ?? null)) {
            if ('' !== $decorates && '@' === $decorates[0]) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The value of the "decorates" option for the "%s" service must be the id of the service without the "@" prefix (replace "%s" with "%s").', $id, $service['decorates'], \substr($decorates, 1)));
            }
            $decorationOnInvalid = \array_key_exists('decoration_on_invalid', $service) ? $service['decoration_on_invalid'] : 'exception';
            if ('exception' === $decorationOnInvalid) {
                $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
            } elseif ('ignore' === $decorationOnInvalid) {
                $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE;
            } elseif (null === $decorationOnInvalid) {
                $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::NULL_ON_INVALID_REFERENCE;
            } elseif ('null' === $decorationOnInvalid) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid value "%s" for attribute "decoration_on_invalid" on service "%s". Did you mean null (without quotes) in "%s"?', $decorationOnInvalid, $id, $file));
            } else {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid value "%s" for attribute "decoration_on_invalid" on service "%s". Did you mean "exception", "ignore" or null in "%s"?', $decorationOnInvalid, $id, $file));
            }
            $renameId = isset($service['decoration_inner_name']) ? $service['decoration_inner_name'] : null;
            $priority = isset($service['decoration_priority']) ? $service['decoration_priority'] : 0;
            $definition->setDecoratedService($decorates, $renameId, $priority, $invalidBehavior);
        }
        if (isset($service['autowire'])) {
            $definition->setAutowired($service['autowire']);
        }
        if (isset($defaults['bind']) || isset($service['bind'])) {
            // deep clone, to avoid multiple process of the same instance in the passes
            $bindings = isset($defaults['bind']) ? \unserialize(\serialize($defaults['bind'])) : [];
            if (isset($service['bind'])) {
                if (!\is_array($service['bind'])) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Parameter "bind" must be an array for service "%s" in "%s". Check your YAML syntax.', $id, $file));
                }
                $bindings = \array_merge($bindings, $this->resolveServices($service['bind'], $file));
                $bindingType = $this->isLoadingInstanceof ? \Builderius\Symfony\Component\DependencyInjection\Argument\BoundArgument::INSTANCEOF_BINDING : \Builderius\Symfony\Component\DependencyInjection\Argument\BoundArgument::SERVICE_BINDING;
                foreach ($bindings as $argument => $value) {
                    if (!$value instanceof \Builderius\Symfony\Component\DependencyInjection\Argument\BoundArgument) {
                        $bindings[$argument] = new \Builderius\Symfony\Component\DependencyInjection\Argument\BoundArgument($value, \true, $bindingType, $file);
                    }
                }
            }
            $definition->setBindings($bindings);
        }
        if (isset($service['autoconfigure'])) {
            if (!$definition instanceof \Builderius\Symfony\Component\DependencyInjection\ChildDefinition) {
                $definition->setAutoconfigured($service['autoconfigure']);
            } elseif ($service['autoconfigure']) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The service "%s" cannot have a "parent" and also have "autoconfigure". Try setting "autoconfigure: false" for the service.', $id));
            }
        }
        if (\array_key_exists('namespace', $service) && !\array_key_exists('resource', $service)) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('A "resource" attribute must be set when the "namespace" attribute is set for service "%s" in "%s". Check your YAML syntax.', $id, $file));
        }
        if (\array_key_exists('resource', $service)) {
            if (!\is_string($service['resource'])) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('A "resource" attribute must be of type string for service "%s" in "%s". Check your YAML syntax.', $id, $file));
            }
            $exclude = isset($service['exclude']) ? $service['exclude'] : null;
            $namespace = isset($service['namespace']) ? $service['namespace'] : $id;
            $this->registerClasses($definition, $namespace, $service['resource'], $exclude);
        } else {
            $this->setDefinition($id, $definition);
        }
    }
    /**
     * Parses a callable.
     *
     * @param string|array $callable A callable reference
     *
     * @throws InvalidArgumentException When errors occur
     *
     * @return string|array|Reference A parsed callable
     */
    private function parseCallable($callable, string $parameter, string $id, string $file)
    {
        if (\is_string($callable)) {
            if ('' !== $callable && '@' === $callable[0]) {
                if (\false === \strpos($callable, ':')) {
                    return [$this->resolveServices($callable, $file), '__invoke'];
                }
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The value of the "%s" option for the "%s" service must be the id of the service without the "@" prefix (replace "%s" with "%s" in "%s").', $parameter, $id, $callable, \substr($callable, 1), $file));
            }
            return $callable;
        }
        if (\is_array($callable)) {
            if (isset($callable[0]) && isset($callable[1])) {
                return [$this->resolveServices($callable[0], $file), $callable[1]];
            }
            if ('factory' === $parameter && isset($callable[1]) && null === $callable[0]) {
                return $callable;
            }
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Parameter "%s" must contain an array with two elements for service "%s" in "%s". Check your YAML syntax.', $parameter, $id, $file));
        }
        throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Parameter "%s" must be a string or an array for service "%s" in "%s". Check your YAML syntax.', $parameter, $id, $file));
    }
    /**
     * Loads a YAML file.
     *
     * @param string $file
     *
     * @return array The file content
     *
     * @throws InvalidArgumentException when the given file is not a local file or when it does not exist
     */
    protected function loadFile($file)
    {
        if (!\class_exists('Builderius\\Symfony\\Component\\Yaml\\Parser')) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\RuntimeException('Unable to load YAML config files as the Symfony Yaml Component is not installed.');
        }
        if (!\stream_is_local($file)) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('This is not a local file "%s".', $file));
        }
        if (!\file_exists($file)) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The file "%s" does not exist.', $file));
        }
        if (null === $this->yamlParser) {
            $this->yamlParser = new \Builderius\Symfony\Component\Yaml\Parser();
        }
        try {
            $configuration = $this->yamlParser->parseFile($file, \Builderius\Symfony\Component\Yaml\Yaml::PARSE_CONSTANT | \Builderius\Symfony\Component\Yaml\Yaml::PARSE_CUSTOM_TAGS);
        } catch (\Builderius\Symfony\Component\Yaml\Exception\ParseException $e) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The file "%s" does not contain valid YAML: ', $file) . $e->getMessage(), 0, $e);
        }
        return $this->validate($configuration, $file);
    }
    /**
     * Validates a YAML file.
     *
     * @throws InvalidArgumentException When service file is not valid
     */
    private function validate($content, string $file) : ?array
    {
        if (null === $content) {
            return $content;
        }
        if (!\is_array($content)) {
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The service file "%s" is not valid. It should contain an array. Check your YAML syntax.', $file));
        }
        foreach ($content as $namespace => $data) {
            if (\in_array($namespace, ['imports', 'parameters', 'services'])) {
                continue;
            }
            if (!$this->container->hasExtension($namespace)) {
                $extensionNamespaces = \array_filter(\array_map(function (\Builderius\Symfony\Component\DependencyInjection\Extension\ExtensionInterface $ext) {
                    return $ext->getAlias();
                }, $this->container->getExtensions()));
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('There is no extension able to load the configuration for "%s" (in "%s"). Looked for namespace "%s", found "%s".', $namespace, $file, $namespace, $extensionNamespaces ? \sprintf('"%s"', \implode('", "', $extensionNamespaces)) : 'none'));
            }
        }
        return $content;
    }
    /**
     * Resolves services.
     *
     * @return array|string|Reference|ArgumentInterface
     */
    private function resolveServices($value, string $file, bool $isParameter = \false)
    {
        if ($value instanceof \Builderius\Symfony\Component\Yaml\Tag\TaggedValue) {
            $argument = $value->getValue();
            if ('iterator' === $value->getTag()) {
                if (!\is_array($argument)) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('"!iterator" tag only accepts sequences in "%s".', $file));
                }
                $argument = $this->resolveServices($argument, $file, $isParameter);
                try {
                    return new \Builderius\Symfony\Component\DependencyInjection\Argument\IteratorArgument($argument);
                } catch (\Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException $e) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('"!iterator" tag only accepts arrays of "@service" references in "%s".', $file));
                }
            }
            if ('service_locator' === $value->getTag()) {
                if (!\is_array($argument)) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('"!service_locator" tag only accepts maps in "%s".', $file));
                }
                $argument = $this->resolveServices($argument, $file, $isParameter);
                try {
                    return new \Builderius\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument($argument);
                } catch (\Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException $e) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('"!service_locator" tag only accepts maps of "@service" references in "%s".', $file));
                }
            }
            if (\in_array($value->getTag(), ['tagged', 'tagged_iterator', 'tagged_locator'], \true)) {
                $forLocator = 'tagged_locator' === $value->getTag();
                if (\is_array($argument) && isset($argument['tag']) && $argument['tag']) {
                    if ($diff = \array_diff(\array_keys($argument), ['tag', 'index_by', 'default_index_method', 'default_priority_method'])) {
                        throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('"!%s" tag contains unsupported key "%s"; supported ones are "tag", "index_by", "default_index_method", and "default_priority_method".', $value->getTag(), \implode('", "', $diff)));
                    }
                    $argument = new \Builderius\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument($argument['tag'], $argument['index_by'] ?? null, $argument['default_index_method'] ?? null, $forLocator, $argument['default_priority_method'] ?? null);
                } elseif (\is_string($argument) && $argument) {
                    $argument = new \Builderius\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument($argument, null, null, $forLocator);
                } else {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('"!%s" tags only accept a non empty string or an array with a key "tag" in "%s".', $value->getTag(), $file));
                }
                if ($forLocator) {
                    $argument = new \Builderius\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument($argument);
                }
                return $argument;
            }
            if ('service' === $value->getTag()) {
                if ($isParameter) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Using an anonymous service in a parameter is not allowed in "%s".', $file));
                }
                $isLoadingInstanceof = $this->isLoadingInstanceof;
                $this->isLoadingInstanceof = \false;
                $instanceof = $this->instanceof;
                $this->instanceof = [];
                $id = \sprintf('.%d_%s', ++$this->anonymousServicesCount, \preg_replace('/^.*\\\\/', '', isset($argument['class']) ? $argument['class'] : '') . $this->anonymousServicesSuffix);
                $this->parseDefinition($id, $argument, $file, []);
                if (!$this->container->hasDefinition($id)) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Creating an alias using the tag "!service" is not allowed in "%s".', $file));
                }
                $this->container->getDefinition($id)->setPublic(\false);
                $this->isLoadingInstanceof = $isLoadingInstanceof;
                $this->instanceof = $instanceof;
                return new \Builderius\Symfony\Component\DependencyInjection\Reference($id);
            }
            throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Unsupported tag "!%s".', $value->getTag()));
        }
        if (\is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->resolveServices($v, $file, $isParameter);
            }
        } elseif (\is_string($value) && 0 === \strpos($value, '@=')) {
            if (!\class_exists(\Builderius\Symfony\Component\ExpressionLanguage\Expression::class)) {
                throw new \LogicException(\sprintf('The "@=" expression syntax cannot be used without the ExpressionLanguage component. Try running "composer require symfony/expression-language".'));
            }
            return new \Builderius\Symfony\Component\ExpressionLanguage\Expression(\substr($value, 2));
        } elseif (\is_string($value) && 0 === \strpos($value, '@')) {
            if (0 === \strpos($value, '@@')) {
                $value = \substr($value, 1);
                $invalidBehavior = null;
            } elseif (0 === \strpos($value, '@!')) {
                $value = \substr($value, 2);
                $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE;
            } elseif (0 === \strpos($value, '@?')) {
                $value = \substr($value, 2);
                $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE;
            } else {
                $value = \substr($value, 1);
                $invalidBehavior = \Builderius\Symfony\Component\DependencyInjection\ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
            }
            if (null !== $invalidBehavior) {
                $value = new \Builderius\Symfony\Component\DependencyInjection\Reference($value, $invalidBehavior);
            }
        }
        return $value;
    }
    /**
     * Loads from Extensions.
     */
    private function loadFromExtensions(array $content)
    {
        foreach ($content as $namespace => $values) {
            if (\in_array($namespace, ['imports', 'parameters', 'services'])) {
                continue;
            }
            if (!\is_array($values) && null !== $values) {
                $values = [];
            }
            $this->container->loadFromExtension($namespace, $values);
        }
    }
    /**
     * Checks the keywords used to define a service.
     */
    private function checkDefinition(string $id, array $definition, string $file)
    {
        if ($this->isLoadingInstanceof) {
            $keywords = self::$instanceofKeywords;
        } elseif (isset($definition['resource']) || isset($definition['namespace'])) {
            $keywords = self::$prototypeKeywords;
        } else {
            $keywords = self::$serviceKeywords;
        }
        foreach ($definition as $key => $value) {
            if (!isset($keywords[$key])) {
                throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('The configuration key "%s" is unsupported for definition "%s" in "%s". Allowed configuration keys are "%s".', $key, $id, $file, \implode('", "', $keywords)));
            }
        }
    }
}
