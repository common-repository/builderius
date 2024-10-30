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

use Builderius\Symfony\Component\ExpressionLanguage\Expression;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFunction;
/**
 * ExpressionExtension gives a way to create Expressions from a template.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class ExpressionExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return [new \Builderius\Twig\TwigFunction('expression', [$this, 'createExpression'])];
    }
    public function createExpression(string $expression) : \Builderius\Symfony\Component\ExpressionLanguage\Expression
    {
        return new \Builderius\Symfony\Component\ExpressionLanguage\Expression($expression);
    }
}
