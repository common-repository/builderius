<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\Twig\Extension;

use Builderius\Symfony\Bridge\Twig\TokenParser\DumpTokenParser;
use Builderius\Symfony\Component\VarDumper\Cloner\ClonerInterface;
use Builderius\Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Builderius\Twig\Environment;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\Template;
use Builderius\Twig\TwigFunction;
/**
 * Provides integration of the dump() function with Twig.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class DumpExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    private $cloner;
    private $dumper;
    public function __construct(\Builderius\Symfony\Component\VarDumper\Cloner\ClonerInterface $cloner, \Builderius\Symfony\Component\VarDumper\Dumper\HtmlDumper $dumper = null)
    {
        $this->cloner = $cloner;
        $this->dumper = $dumper;
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return [new \Builderius\Twig\TwigFunction('dump', [$this, 'dump'], ['is_safe' => ['html'], 'needs_context' => \true, 'needs_environment' => \true])];
    }
    /**
     * {@inheritdoc}
     */
    public function getTokenParsers() : array
    {
        return [new \Builderius\Symfony\Bridge\Twig\TokenParser\DumpTokenParser()];
    }
    public function dump(\Builderius\Twig\Environment $env, array $context) : ?string
    {
        if (!$env->isDebug()) {
            return null;
        }
        if (2 === \func_num_args()) {
            $vars = [];
            foreach ($context as $key => $value) {
                if (!$value instanceof \Builderius\Twig\Template) {
                    $vars[$key] = $value;
                }
            }
            $vars = [$vars];
        } else {
            $vars = \func_get_args();
            unset($vars[0], $vars[1]);
        }
        $dump = \fopen('php://memory', 'r+b');
        $this->dumper = $this->dumper ?: new \Builderius\Symfony\Component\VarDumper\Dumper\HtmlDumper();
        $this->dumper->setCharset($env->getCharset());
        foreach ($vars as $value) {
            $this->dumper->dump($this->cloner->cloneVar($value), $dump);
        }
        return \stream_get_contents($dump, -1, 0);
    }
}
