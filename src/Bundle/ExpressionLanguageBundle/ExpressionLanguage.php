<?php

namespace Builderius\Bundle\ExpressionLanguageBundle;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Psr\Cache\CacheItemPoolInterface;
use Builderius\Symfony\Component\Cache\Adapter\ArrayAdapter;
use Builderius\Symfony\Component\ExpressionLanguage\Compiler;
use Builderius\Symfony\Component\ExpressionLanguage\Expression;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Builderius\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Builderius\Symfony\Component\ExpressionLanguage\ParsedExpression;

class ExpressionLanguage extends \Builderius\Symfony\Component\ExpressionLanguage\ExpressionLanguage
{
    private $cache;
    private $lexer;
    private $parser;
    private $compiler;
    protected $functions = [];

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $tmpVarsContext;

    /**
     * @param ExpressionFunctionProviderInterface[] $providers
     */
    public function __construct(CacheItemPoolInterface $cache = null, array $providers = [])
    {
        $this->cache = $cache ?: new ArrayAdapter();
        $this->registerFunctions();
        foreach ($providers as $provider) {
            $this->registerProvider($provider);
        }
    }

    public function setTmpVarsContext(BuilderiusRuntimeObjectCache $tmpVarsContext)
    {
        $this->tmpVarsContext = $tmpVarsContext;
    }

    /**
     * Compiles an expression source code.
     *
     * @param Expression|string $expression The expression to compile
     *
     * @return string The compiled PHP source code
     */
    public function compile($expression, array $names = [])
    {
        return $this->getCompiler()->compile($this->parse($expression, $names)->getNodes())->getSource();
    }
    /**
     * Evaluate an expression.
     *
     * @param Expression|string $expression The expression to compile
     *
     * @return mixed The result of the evaluation of the expression
     */
    public function evaluate($expression, array $values = [])
    {
        if ($this->tmpVarsContext) {
            $this->tmpVarsContext->flush();
        }

        return $this->parse($expression, \array_keys($values))->getNodes()->evaluate($this->functions, $values);
    }
    /**
     * Parses an expression.
     *
     * @param Expression|string $expression The expression to parse
     *
     * @return ParsedExpression A ParsedExpression instance
     */
    public function parse($expression, array $names)
    {
        if ($expression instanceof ParsedExpression) {
            return $expression;
        }
        \asort($names);
        $cacheKeyItems = [];
        foreach ($names as $nameKey => $name) {
            $cacheKeyItems[] = \is_int($nameKey) ? $name : $nameKey . ':' . $name;
        }
        $cacheItem = $this->cache->getItem(\rawurlencode($expression . '//' . \implode('|', $cacheKeyItems)));
        if (null === ($parsedExpression = $cacheItem->get())) {
            $nodes = $this->getParser()->parse($this->getLexer()->tokenize((string) $expression), $names);
            $parsedExpression = new ParsedExpression((string) $expression, $nodes);
            $cacheItem->set($parsedExpression);
            $this->cache->save($cacheItem);
        }
        return $parsedExpression;
    }
    /**
     * Registers a function.
     *
     * @param callable $compiler  A callable able to compile the function
     * @param callable $evaluator A callable able to evaluate the function
     *
     * @throws \LogicException when registering a function after calling evaluate(), compile() or parse()
     *
     * @see ExpressionFunction
     */
    public function register(string $name, callable $compiler, callable $evaluator)
    {
        if (null !== $this->parser) {
            throw new \LogicException('Registering functions after calling evaluate(), compile() or parse() is not supported.');
        }
        $this->functions[$name] = ['compiler' => $compiler, 'evaluator' => $evaluator];
    }
    public function addFunction(ExpressionFunction $function)
    {
        $this->register($function->getName(), $function->getCompiler(), $function->getEvaluator());
    }
    public function registerProvider(ExpressionFunctionProviderInterface $provider)
    {
        foreach ($provider->getFunctions() as $function) {
            $this->addFunction($function);
        }
    }
    protected function registerFunctions()
    {
        $this->addFunction(ExpressionFunction::fromPhp('constant'));
    }
    private function getLexer()
    {
        if (null === $this->lexer) {
            $this->lexer = new Lexer();
        }
        return $this->lexer;
    }
    private function getParser()
    {
        if (null === $this->parser) {
            $this->parser = new Parser($this->functions);
        }
        return $this->parser;
    }
    private function getCompiler()
    {
        if (null === $this->compiler) {
            $this->compiler = new Compiler($this->functions);
        }
        return $this->compiler->reset();
    }
}