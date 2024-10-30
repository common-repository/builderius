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
use Builderius\Twig\Node\Node;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class TransDefaultDomainNode extends \Builderius\Twig\Node\Node
{
    public function __construct(\Builderius\Twig\Node\Expression\AbstractExpression $expr, int $lineno = 0, string $tag = null)
    {
        parent::__construct(['expr' => $expr], [], $lineno, $tag);
    }
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        // noop as this node is just a marker for TranslationDefaultDomainNodeVisitor
    }
}
