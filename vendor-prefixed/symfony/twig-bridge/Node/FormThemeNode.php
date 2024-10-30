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

use Builderius\Symfony\Component\Form\FormRenderer;
use Builderius\Twig\Compiler;
use Builderius\Twig\Node\Node;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class FormThemeNode extends \Builderius\Twig\Node\Node
{
    public function __construct(\Builderius\Twig\Node\Node $form, \Builderius\Twig\Node\Node $resources, int $lineno, string $tag = null, bool $only = \false)
    {
        parent::__construct(['form' => $form, 'resources' => $resources], ['only' => $only], $lineno, $tag);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this)->write('$this->env->getRuntime(')->string(\Builderius\Symfony\Component\Form\FormRenderer::class)->raw(')->setTheme(')->subcompile($this->getNode('form'))->raw(', ')->subcompile($this->getNode('resources'))->raw(', ')->raw(\false === $this->getAttribute('only') ? 'true' : 'false')->raw(");\n");
    }
}
