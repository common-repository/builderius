<?php

namespace Builderius\Mustache;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Mustache\Cache\FilesystemCache;
use Builderius\Mustache\Cache\NoopCache;
use Builderius\Mustache\Exception\InvalidArgumentException;
use Builderius\Mustache\Exception\RuntimeException;
use Builderius\Mustache\Exception\UnknownTemplateException;
use Builderius\Mustache\Loader\ArrayLoader;
use Builderius\Mustache\Loader\MutableLoader;
use Builderius\Mustache\Loader\StringLoader;
use Builderius\Mustache\Template;
/**
 * A Mustache implementation in PHP.
 *
 * {@link http://defunkt.github.com/mustache}
 *
 * Mustache is a framework-agnostic logic-less templating language. It enforces separation of view
 * logic from template files. In fact, it is not even possible to embed logic in the template.
 *
 * This is very, very rad.
 *
 * @author Justin Hileman {@link http://justinhileman.com}
 */
class Engine
{
    const VERSION = '2.14.2';
    const SPEC_VERSION = '1.2.2';
    const PRAGMA_FILTERS = 'FILTERS';
    const PRAGMA_BLOCKS = 'BLOCKS';
    const PRAGMA_ANCHORED_DOT = 'ANCHORED-DOT';
    // Known pragmas
    private static $knownPragmas = array(self::PRAGMA_FILTERS => \true, self::PRAGMA_BLOCKS => \true, self::PRAGMA_ANCHORED_DOT => \true);
    // Template cache
    private $templates = array();
    // Environment
    private $templateClassPrefix = '__';
    private $cache;
    private $lambdaCache;
    private $cacheLambdaTemplates = \false;
    private $loader;
    private $partialsLoader;
    private $helpers;
    private $escape;
    private $entityFlags = \ENT_COMPAT;
    private $charset = 'UTF-8';
    private $logger;
    private $strictCallables = \false;
    private $pragmas = array();
    private $delimiters;
    // Services
    private $tokenizer;
    private $parser;
    private $compiler;
    private $expressionLanguage;

    private $globalContext = [];
    /**
     * Mustache class constructor.
     *
     * Passing an $options array allows overriding certain Mustache options during instantiation:
     *
     *     $options = array(
     *         // The class prefix for compiled templates. Defaults to '__'.
     *         'template_class_prefix' => '__MyTemplates_',
     *
     *         // A Mustache cache instance or a cache directory string for compiled templates.
     *         // Mustache will not cache templates unless this is set.
     *         'cache' => dirname(__FILE__).'/tmp/cache/mustache',
     *
     *         // Override default permissions for cache files. Defaults to using the system-defined umask. It is
     *         // *strongly* recommended that you configure your umask properly rather than overriding permissions here.
     *         'cache_file_mode' => 0666,
     *
     *         // Optionally, enable caching for lambda section templates. This is generally not recommended, as lambda
     *         // sections are often too dynamic to benefit from caching.
     *         'cache_lambda_templates' => true,
     *
     *         // Customize the tag delimiters used by this engine instance. Note that overriding here changes the
     *         // delimiters used to parse all templates and partials loaded by this instance. To override just for a
     *         // single template, use an inline "change delimiters" tag at the start of the template file:
     *         //
     *         //     {{=<% %>=}}
     *         //
     *         'delimiters' => '<% %>',
     *
     *         // A Mustache template loader instance. Uses a StringLoader if not specified.
     *         'loader' => new Loader_FilesystemLoader(dirname(__FILE__).'/views'),
     *
     *         // A Mustache loader instance for partials.
     *         'partials_loader' => new Loader_FilesystemLoader(dirname(__FILE__).'/views/partials'),
     *
     *         // An array of Mustache partials. Useful for quick-and-dirty string template loading, but not as
     *         // efficient or lazy as a Filesystem (or database) loader.
     *         'partials' => array('foo' => file_get_contents(dirname(__FILE__).'/views/partials/foo.mustache')),
     *
     *         // An array of 'helpers'. Helpers can be global variables or objects, closures (e.g. for higher order
     *         // sections), or any other valid Mustache context value. They will be prepended to the context stack,
     *         // so they will be available in any template loaded by this Mustache instance.
     *         'helpers' => array('i18n' => function ($text) {
     *             // do something translatey here...
     *         }),
     *
     *         // An 'escape' callback, responsible for escaping double-mustache variables.
     *         'escape' => function ($value) {
     *             return htmlspecialchars($buffer, ENT_COMPAT, 'UTF-8');
     *         },
     *
     *         // Type argument for `htmlspecialchars`.  Defaults to ENT_COMPAT.  You may prefer ENT_QUOTES.
     *         'entity_flags' => ENT_QUOTES,
     *
     *         // Character set for `htmlspecialchars`. Defaults to 'UTF-8'. Use 'UTF-8'.
     *         'charset' => 'ISO-8859-1',
     *
     *         // A Mustache Logger instance. No logging will occur unless this is set. Using a PSR-3 compatible
     *         // logging library -- such as Monolog -- is highly recommended. A simple stream logger implementation is
     *         // available as well:
     *         'logger' => new Logger_StreamLogger('php://stderr'),
     *
     *         // Only treat Closure instances and invokable classes as callable. If true, values like
     *         // `array('ClassName', 'methodName')` and `array($classInstance, 'methodName')`, which are traditionally
     *         // "callable" in PHP, are not called to resolve variables for interpolation or section contexts. This
     *         // helps protect against arbitrary code execution when user input is passed directly into the template.
     *         // This currently defaults to false, but will default to true in v3.0.
     *         'strict_callables' => true,
     *
     *         // Enable pragmas across all templates, regardless of the presence of pragma tags in the individual
     *         // templates.
     *         'pragmas' => [Engine::PRAGMA_FILTERS],
     *     );
     *
     * @throws InvalidArgumentException If `escape` option is not callable
     *
     * @param array $options (default: array())
     */
    public function __construct(array $options = array())
    {
        if (isset($options['template_class_prefix'])) {
            if ((string) $options['template_class_prefix'] === '') {
                throw new \Builderius\Mustache\Exception\InvalidArgumentException('Mustache Constructor "template_class_prefix" must not be empty');
            }
            $this->templateClassPrefix = $options['template_class_prefix'];
        }
        if (isset($options['cache'])) {
            $cache = $options['cache'];
            if (\is_string($cache)) {
                $mode = isset($options['cache_file_mode']) ? $options['cache_file_mode'] : null;
                $cache = new \Builderius\Mustache\Cache\FilesystemCache($cache, $mode);
            }
            $this->setCache($cache);
        }
        if (isset($options['cache_lambda_templates'])) {
            $this->cacheLambdaTemplates = (bool) $options['cache_lambda_templates'];
        }
        if (isset($options['loader'])) {
            $this->setLoader($options['loader']);
        }
        if (isset($options['partials_loader'])) {
            $this->setPartialsLoader($options['partials_loader']);
        }
        if (isset($options['partials'])) {
            $this->setPartials($options['partials']);
        }
        if (isset($options['helpers'])) {
            $this->setHelpers($options['helpers']);
        }
        if (isset($options['escape'])) {
            if (!\is_callable($options['escape'])) {
                throw new \Builderius\Mustache\Exception\InvalidArgumentException('Mustache Constructor "escape" option must be callable');
            }
            $this->escape = $options['escape'];
        }
        if (isset($options['entity_flags'])) {
            $this->entityFlags = $options['entity_flags'];
        }
        if (isset($options['charset'])) {
            $this->charset = $options['charset'];
        }
        if (isset($options['logger'])) {
            $this->setLogger($options['logger']);
        }
        if (isset($options['strict_callables'])) {
            $this->strictCallables = $options['strict_callables'];
        }
        if (isset($options['delimiters'])) {
            $this->delimiters = $options['delimiters'];
        }
        if (isset($options['pragmas'])) {
            foreach ($options['pragmas'] as $pragma) {
                if (!isset(self::$knownPragmas[$pragma])) {
                    throw new \Builderius\Mustache\Exception\InvalidArgumentException(\sprintf('Unknown pragma: "%s".', $pragma));
                }
                $this->pragmas[$pragma] = \true;
            }
        }
    }
    /**
     * Shortcut 'render' invocation.
     *
     * Equivalent to calling `$mustache->loadTemplate($template)->render($context);`
     *
     * @see Engine::loadTemplate
     * @see Template::render
     *
     * @param string $template
     * @param mixed  $context  (default: array())
     *
     * @return string Rendered template
     */
    public function render($template, $context = [], $globalContext = [])
    {
        $this->setGlobalContext($globalContext);

        return $this->loadTemplate($template)->render($context, $globalContext);
    }
    /**
     * Get the current Mustache escape callback.
     *
     * @return callable|null
     */
    public function getEscape()
    {
        return $this->escape;
    }
    /**
     * Get the current Mustache entitity type to escape.
     *
     * @return int
     */
    public function getEntityFlags()
    {
        return $this->entityFlags;
    }
    /**
     * Get the current Mustache character set.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }
    /**
     * Get the current globally enabled pragmas.
     *
     * @return array
     */
    public function getPragmas()
    {
        return \array_keys($this->pragmas);
    }
    /**
     * Set the Mustache template Loader instance.
     *
     * @param Loader $loader
     */
    public function setLoader(\Builderius\Mustache\Loader $loader)
    {
        $this->loader = $loader;
    }
    /**
     * Get the current Mustache template Loader instance.
     *
     * If no Loader instance has been explicitly specified, this method will instantiate and return
     * a StringLoader instance.
     *
     * @return Loader
     */
    public function getLoader()
    {
        if (!isset($this->loader)) {
            $this->loader = new \Builderius\Mustache\Loader\StringLoader();
        }
        return $this->loader;
    }
    /**
     * Set the Mustache partials Loader instance.
     *
     * @param Loader $partialsLoader
     */
    public function setPartialsLoader(\Builderius\Mustache\Loader $partialsLoader)
    {
        $this->partialsLoader = $partialsLoader;
    }
    /**
     * Get the current Mustache partials Loader instance.
     *
     * If no Loader instance has been explicitly specified, this method will instantiate and return
     * an ArrayLoader instance.
     *
     * @return Loader
     */
    public function getPartialsLoader()
    {
        if (!isset($this->partialsLoader)) {
            $this->partialsLoader = new \Builderius\Mustache\Loader\ArrayLoader();
        }
        return $this->partialsLoader;
    }
    /**
     * Set partials for the current partials Loader instance.
     *
     * @throws RuntimeException If the current Loader instance is immutable
     *
     * @param array $partials (default: array())
     */
    public function setPartials(array $partials = array())
    {
        if (!isset($this->partialsLoader)) {
            $this->partialsLoader = new \Builderius\Mustache\Loader\ArrayLoader();
        }
        if (!$this->partialsLoader instanceof \Builderius\Mustache\Loader\MutableLoader) {
            throw new \Builderius\Mustache\Exception\RuntimeException('Unable to set partials on an immutable Mustache Loader instance');
        }
        $this->partialsLoader->setTemplates($partials);
    }
    /**
     * Set an array of Mustache helpers.
     *
     * An array of 'helpers'. Helpers can be global variables or objects, closures (e.g. for higher order sections), or
     * any other valid Mustache context value. They will be prepended to the context stack, so they will be available in
     * any template loaded by this Mustache instance.
     *
     * @throws InvalidArgumentException if $helpers is not an array or Traversable
     *
     * @param array|\Traversable $helpers
     */
    public function setHelpers($helpers)
    {
        if (!\is_array($helpers) && !$helpers instanceof \Traversable) {
            throw new \Builderius\Mustache\Exception\InvalidArgumentException('setHelpers expects an array of helpers');
        }
        $this->getHelpers()->clear();
        foreach ($helpers as $name => $helper) {
            $this->addHelper($name, $helper);
        }
    }
    /**
     * Get the current set of Mustache helpers.
     *
     * @see Engine::setHelpers
     *
     * @return HelperCollection
     */
    public function getHelpers()
    {
        if (!isset($this->helpers)) {
            $this->helpers = new \Builderius\Mustache\HelperCollection();
        }
        return $this->helpers;
    }
    /**
     * Add a new Mustache helper.
     *
     * @see Engine::setHelpers
     *
     * @param string $name
     * @param mixed  $helper
     */
    public function addHelper($name, $helper)
    {
        $this->getHelpers()->add($name, $helper);
    }
    /**
     * Get a Mustache helper by name.
     *
     * @see Engine::setHelpers
     *
     * @param string $name
     *
     * @return mixed Helper
     */
    public function getHelper($name)
    {
        return $this->getHelpers()->get($name);
    }
    /**
     * Check whether this Mustache instance has a helper.
     *
     * @see Engine::setHelpers
     *
     * @param string $name
     *
     * @return bool True if the helper is present
     */
    public function hasHelper($name)
    {
        return $this->getHelpers()->has($name);
    }
    /**
     * Remove a helper by name.
     *
     * @see Engine::setHelpers
     *
     * @param string $name
     */
    public function removeHelper($name)
    {
        $this->getHelpers()->remove($name);
    }
    /**
     * Set the Mustache Logger instance.
     *
     * @throws InvalidArgumentException If logger is not an instance of Logger or Psr\Log\LoggerInterface
     *
     * @param Logger $logger
     */
    public function setLogger($logger = null)
    {
        if ($logger !== null && !($logger instanceof \Builderius\Mustache\Logger || \is_a($logger, 'Builderius\\Psr\\Log\\LoggerInterface'))) {
            throw new \Builderius\Mustache\Exception\InvalidArgumentException('Expected an instance of Logger or Psr\\Log\\LoggerInterface.');
        }
        if ($this->getCache()->getLogger() === null) {
            $this->getCache()->setLogger($logger);
        }
        $this->logger = $logger;
    }
    /**
     * Get the current Mustache Logger instance.
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
    /**
     * Set the Mustache Tokenizer instance.
     *
     * @param Tokenizer $tokenizer
     */
    public function setTokenizer(\Builderius\Mustache\Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }
    /**
     * Get the current Mustache Tokenizer instance.
     *
     * If no Tokenizer instance has been explicitly specified, this method will instantiate and return a new one.
     *
     * @return Tokenizer
     */
    public function getTokenizer()
    {
        if (!isset($this->tokenizer)) {
            $this->tokenizer = new \Builderius\Mustache\Tokenizer();
        }
        return $this->tokenizer;
    }
    /**
     * Set the Mustache Parser instance.
     *
     * @param Parser $parser
     */
    public function setParser(\Builderius\Mustache\Parser $parser)
    {
        $this->parser = $parser;
    }
    /**
     * Get the current Mustache Parser instance.
     *
     * If no Parser instance has been explicitly specified, this method will instantiate and return a new one.
     *
     * @return Parser
     */
    public function getParser()
    {
        if (!isset($this->parser)) {
            $this->parser = new \Builderius\Mustache\Parser();
        }
        return $this->parser;
    }
    /**
     * Set the Mustache Compiler instance.
     *
     * @param Compiler $compiler
     */
    public function setCompiler(\Builderius\Mustache\Compiler $compiler)
    {
        $this->compiler = $compiler;
    }
    /**
     * Get the current Mustache Compiler instance.
     *
     * If no Compiler instance has been explicitly specified, this method will instantiate and return a new one.
     *
     * @return Compiler
     */
    public function getCompiler()
    {
        if (!isset($this->compiler)) {
            $this->compiler = new \Builderius\Mustache\Compiler();
        }
        return $this->compiler;
    }
    /**
     * Set the Mustache Cache instance.
     *
     * @param Cache $cache
     */
    public function setCache(\Builderius\Mustache\Cache $cache)
    {
        if (isset($this->logger) && $cache->getLogger() === null) {
            $cache->setLogger($this->getLogger());
        }
        $this->cache = $cache;
    }
    /**
     * Get the current Mustache Cache instance.
     *
     * If no Cache instance has been explicitly specified, this method will instantiate and return a new one.
     *
     * @return Cache
     */
    public function getCache()
    {
        if (!isset($this->cache)) {
            $this->setCache(new \Builderius\Mustache\Cache\NoopCache());
        }
        return $this->cache;
    }
    /**
     * @return mixed
     */
    public function getExpressionLanguage()
    {
        return $this->expressionLanguage;
    }
    /**
     * @param mixed $expressionLanguage
     * @return $this
     */
    public function setExpressionLanguage($expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getGlobalContext()
    {
        return $this->globalContext;
    }
    /**
     * @param array $globalContext
     * @return $this
     */
    public function setGlobalContext(array $globalContext = null)
    {
        $this->globalContext = $globalContext;
        return $this;
    }
    /**
     * Get the current Lambda Cache instance.
     *
     * If 'cache_lambda_templates' is enabled, this is the default cache instance. Otherwise, it is a NoopCache.
     *
     * @see Engine::getCache
     *
     * @return Cache
     */
    protected function getLambdaCache()
    {
        if ($this->cacheLambdaTemplates) {
            return $this->getCache();
        }
        if (!isset($this->lambdaCache)) {
            $this->lambdaCache = new \Builderius\Mustache\Cache\NoopCache();
        }
        return $this->lambdaCache;
    }
    /**
     * Helper method to generate a Mustache template class.
     *
     * This method must be updated any time options are added which make it so
     * the same template could be parsed and compiled multiple different ways.
     *
     * @param string|Source $source
     *
     * @return string Mustache Template class name
     */
    public function getTemplateClassName($source)
    {
        return $this->templateClassPrefix . \md5($source);
    }
    /**
     * Load a Mustache Template by name.
     *
     * @param string $name
     *
     * @return Template
     */
    public function loadTemplate($name)
    {
        return $this->loadSource($this->getLoader()->load($name));
    }
    /**
     * Load a Mustache partial Template by name.
     *
     * This is a helper method used internally by Template instances for loading partial templates. You can most likely
     * ignore it completely.
     *
     * @param string $name
     *
     * @return Template|false
     */
    public function loadPartial($name, $context)
    {
        try {
            if (isset($this->partialsLoader)) {
                $loader = $this->partialsLoader;
            } elseif (isset($this->loader) && !$this->loader instanceof \Builderius\Mustache\Loader\StringLoader) {
                $loader = $this->loader;
            } else {
                throw new \Builderius\Mustache\Exception\UnknownTemplateException($name);
            }
            try {
                $frame = $context->last('.');
                if (is_array($this->globalContext) && is_array($frame)) {
                    $frame = array_merge($this->globalContext, $frame);
                }
                $name = htmlspecialchars_decode($name);
                $name = $this->expressionLanguage->evaluate($name, $frame);
                if (!$name) {
                    return \false;
                }
            } catch (\Exception $e) {
                return \false;
            }
            return $this->loadSource($loader->load($name));
        } catch (\Builderius\Mustache\Exception\UnknownTemplateException $e) {
            // If the named partial cannot be found, log then return null.
            $this->log(\Builderius\Mustache\Logger::WARNING, 'Partial not found: "{name}"', array('name' => $e->getTemplateName()));
        }
    }
    /**
     * Load a Mustache lambda Template by source.
     *
     * This is a helper method used by Template instances to generate subtemplates for Lambda sections. You can most
     * likely ignore it completely.
     *
     * @param string $source
     * @param string $delims (default: null)
     *
     * @return Template
     */
    public function loadLambda($source, $delims = null)
    {
        if ($delims !== null) {
            $source = $delims . "\n" . $source;
        }
        return $this->loadSource($source, $this->getLambdaCache());
    }
    /**
     * Instantiate and return a Mustache Template instance by source.
     *
     * Optionally provide a Cache instance. This is used internally by Engine::loadLambda to respect
     * the 'cache_lambda_templates' configuration option.
     *
     * @see Engine::loadTemplate
     * @see Engine::loadPartial
     * @see Engine::loadLambda
     *
     * @param string|Source $source
     * @param Cache         $cache  (default: null)
     *
     * @return Template
     */
    private function loadSource($source, \Builderius\Mustache\Cache $cache = null)
    {
        $className = $this->getTemplateClassName($source);
        if (!isset($this->templates[$className])) {
            if ($cache === null) {
                $cache = $this->getCache();
            }
            if (!\class_exists($className, \false)) {
                if (!$cache->load($className)) {
                    $compiled = $this->compile($source);
                    $cache->cache($className, $compiled);
                }
            }
            $this->log(\Builderius\Mustache\Logger::DEBUG, 'Instantiating template: "{className}"', array('className' => $className));
            $this->templates[$className] = new $className($this, $this->expressionLanguage);
        }
        return $this->templates[$className];
    }
    /**
     * Helper method to tokenize a Mustache template.
     *
     * @see Tokenizer::scan
     *
     * @param string $source
     *
     * @return array Tokens
     */
    private function tokenize($source)
    {
        return $this->getTokenizer()->scan($source, $this->delimiters);
    }
    /**
     * Helper method to parse a Mustache template.
     *
     * @see Parser::parse
     *
     * @param string $source
     *
     * @return array Token tree
     */
    private function parse($source)
    {
        $parser = $this->getParser();
        $parser->setPragmas($this->getPragmas());
        return $parser->parse($this->tokenize($source));
    }
    /**
     * Helper method to compile a Mustache template.
     *
     * @see Compiler::compile
     *
     * @param string|Source $source
     *
     * @return string generated Mustache template class code
     */
    private function compile($source)
    {
        $name = $this->getTemplateClassName($source);
        $this->log(\Builderius\Mustache\Logger::INFO, 'Compiling template to "{className}" class', array('className' => $name));
        if ($source instanceof \Builderius\Mustache\Source) {
            $source = $source->getSource();
        }
        $tree = $this->parse($source);
        $compiler = $this->getCompiler();
        $compiler->setPragmas($this->getPragmas());
        return $compiler->compile($source, $tree, $name, isset($this->escape), $this->charset, $this->strictCallables, $this->entityFlags);
    }
    /**
     * Add a log record if logging is enabled.
     *
     * @param int    $level   The logging level
     * @param string $message The log message
     * @param array  $context The log context
     */
    private function log($level, $message, array $context = array())
    {
        if (isset($this->logger)) {
            $this->logger->log($level, $message, $context);
        }
    }
}