<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\Twig\Node;

use Builderius\Twig\Compiler;
use Builderius\Twig\Node\Expression\AbstractExpression;
use Builderius\Twig\Node\Expression\ArrayExpression;
use Builderius\Twig\Node\Expression\ConstantExpression;
use Builderius\Twig\Node\Expression\NameExpression;
use Builderius\Twig\Node\Node;
use Builderius\Twig\Node\TextNode;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class TransNode extends \Builderius\Twig\Node\Node
{
    public function __construct(\Builderius\Twig\Node\Node $body, \Builderius\Twig\Node\Node $domain = null, \Builderius\Twig\Node\Expression\AbstractExpression $count = null, \Builderius\Twig\Node\Expression\AbstractExpression $vars = null, \Builderius\Twig\Node\Expression\AbstractExpression $locale = null, int $lineno = 0, string $tag = null)
    {
        $nodes = ['body' => $body];
        if (null !== $domain) {
            $nodes['domain'] = $domain;
        }
        if (null !== $count) {
            $nodes['count'] = $count;
        }
        if (null !== $vars) {
            $nodes['vars'] = $vars;
        }
        if (null !== $locale) {
            $nodes['locale'] = $locale;
        }
        parent::__construct($nodes, [], $lineno, $tag);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this);
        $defaults = new \Builderius\Twig\Node\Expression\ArrayExpression([], -1);
        if ($this->hasNode('vars') && ($vars = $this->getNode('vars')) instanceof \Builderius\Twig\Node\Expression\ArrayExpression) {
            $defaults = $this->getNode('vars');
            $vars = null;
        }
        list($msg, $defaults) = $this->compileString($this->getNode('body'), $defaults, (bool) $vars);
        $compiler->write('echo $this->env->getExtension(\'Symfony\\Bridge\\Twig\\Extension\\TranslationExtension\')->trans(')->subcompile($msg);
        $compiler->raw(', ');
        if (null !== $vars) {
            $compiler->raw('array_merge(')->subcompile($defaults)->raw(', ')->subcompile($this->getNode('vars'))->raw(')');
        } else {
            $compiler->subcompile($defaults);
        }
        $compiler->raw(', ');
        if (!$this->hasNode('domain')) {
            $compiler->repr('messages');
        } else {
            $compiler->subcompile($this->getNode('domain'));
        }
        if ($this->hasNode('locale')) {
            $compiler->raw(', ')->subcompile($this->getNode('locale'));
        } elseif ($this->hasNode('count')) {
            $compiler->raw(', null');
        }
        if ($this->hasNode('count')) {
            $compiler->raw(', ')->subcompile($this->getNode('count'));
        }
        $compiler->raw(");\n");
    }
    private function compileString(\Builderius\Twig\Node\Node $body, \Builderius\Twig\Node\Expression\ArrayExpression $vars, bool $ignoreStrictCheck = \false) : array
    {
        if ($body instanceof \Builderius\Twig\Node\Expression\ConstantExpression) {
            $msg = $body->getAttribute('value');
        } elseif ($body instanceof \Builderius\Twig\Node\TextNode) {
            $msg = $body->getAttribute('data');
        } else {
            return [$body, $vars];
        }
        \preg_match_all('/(?<!%)%([^%]+)%/', $msg, $matches);
        foreach ($matches[1] as $var) {
            $key = new \Builderius\Twig\Node\Expression\ConstantExpression('%' . $var . '%', $body->getTemplateLine());
            if (!$vars->hasElement($key)) {
                if ('count' === $var && $this->hasNode('count')) {
                    $vars->addElement($this->getNode('count'), $key);
                } else {
                    $varExpr = new \Builderius\Twig\Node\Expression\NameExpression($var, $body->getTemplateLine());
                    $varExpr->setAttribute('ignore_strict_check', $ignoreStrictCheck);
                    $vars->addElement($varExpr, $key);
                }
            }
        }
        return [new \Builderius\Twig\Node\Expression\ConstantExpression(\str_replace('%%', '%', \trim($msg)), $body->getTemplateLine()), $vars];
    }
}
