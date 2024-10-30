<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\GeneratorStrategy;

use Builderius\Zend\Code\Generator\ClassGenerator;
/**
 * Generator strategy that produces the code and evaluates it at runtime
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class EvaluatingGeneratorStrategy implements \Builderius\ProxyManager\GeneratorStrategy\GeneratorStrategyInterface
{
    /**
     * @var bool flag indicating whether {@see eval} can be used
     */
    private $canEval = \true;
    /**
     * Constructor
     */
    public function __construct()
    {
        // @codeCoverageIgnoreStart
        $this->canEval = !\ini_get('suhosin.executor.disable_eval');
        // @codeCoverageIgnoreEnd
    }
    /**
     * Evaluates the generated code before returning it
     *
     * {@inheritDoc}
     */
    public function generate(\Builderius\Zend\Code\Generator\ClassGenerator $classGenerator) : string
    {
        $code = $classGenerator->generate();
        // @codeCoverageIgnoreStart
        if (!$this->canEval) {
            $fileName = \tempnam(\sys_get_temp_dir(), 'EvaluatingGeneratorStrategy.php.tmp.');
            \file_put_contents($fileName, "<?php\n" . $code);
            /* @noinspection PhpIncludeInspection */
            require $fileName;
            \unlink($fileName);
            return $code;
        }
        // @codeCoverageIgnoreEnd
        eval($code);
        return $code;
    }
}
