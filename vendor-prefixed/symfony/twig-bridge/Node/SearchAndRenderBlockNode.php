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
use Builderius\Twig\Node\Expression\ArrayExpression;
use Builderius\Twig\Node\Expression\ConstantExpression;
use Builderius\Twig\Node\Expression\FunctionExpression;
/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class SearchAndRenderBlockNode extends \Builderius\Twig\Node\Expression\FunctionExpression
{
    public function compile(\Builderius\Twig\Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this);
        $compiler->raw('$this->env->getRuntime(\'Symfony\\Component\\Form\\FormRenderer\')->searchAndRenderBlock(');
        \preg_match('/_([^_]+)$/', $this->getAttribute('name'), $matches);
        $arguments = \iterator_to_array($this->getNode('arguments'));
        $blockNameSuffix = $matches[1];
        if (isset($arguments[0])) {
            $compiler->subcompile($arguments[0]);
            $compiler->raw(', \'' . $blockNameSuffix . '\'');
            if (isset($arguments[1])) {
                if ('label' === $blockNameSuffix) {
                    // The "label" function expects the label in the second and
                    // the variables in the third argument
                    $label = $arguments[1];
                    $variables = isset($arguments[2]) ? $arguments[2] : null;
                    $lineno = $label->getTemplateLine();
                    if ($label instanceof \Builderius\Twig\Node\Expression\ConstantExpression) {
                        // If the label argument is given as a constant, we can either
                        // strip it away if it is empty, or integrate it into the array
                        // of variables at compile time.
                        $labelIsExpression = \false;
                        // Only insert the label into the array if it is not empty
                        if (!twig_test_empty($label->getAttribute('value'))) {
                            $originalVariables = $variables;
                            $variables = new \Builderius\Twig\Node\Expression\ArrayExpression([], $lineno);
                            $labelKey = new \Builderius\Twig\Node\Expression\ConstantExpression('label', $lineno);
                            if (null !== $originalVariables) {
                                foreach ($originalVariables->getKeyValuePairs() as $pair) {
                                    // Don't copy the original label attribute over if it exists
                                    if ((string) $labelKey !== (string) $pair['key']) {
                                        $variables->addElement($pair['value'], $pair['key']);
                                    }
                                }
                            }
                            // Insert the label argument into the array
                            $variables->addElement($label, $labelKey);
                        }
                    } else {
                        // The label argument is not a constant, but some kind of
                        // expression. This expression needs to be evaluated at runtime.
                        // Depending on the result (whether it is null or not), the
                        // label in the arguments should take precedence over the label
                        // in the attributes or not.
                        $labelIsExpression = \true;
                    }
                } else {
                    // All other functions than "label" expect the variables
                    // in the second argument
                    $label = null;
                    $variables = $arguments[1];
                    $labelIsExpression = \false;
                }
                if (null !== $variables || $labelIsExpression) {
                    $compiler->raw(', ');
                    if (null !== $variables) {
                        $compiler->subcompile($variables);
                    }
                    if ($labelIsExpression) {
                        if (null !== $variables) {
                            $compiler->raw(' + ');
                        }
                        // Check at runtime whether the label is empty.
                        // If not, add it to the array at runtime.
                        $compiler->raw('(twig_test_empty($_label_ = ');
                        $compiler->subcompile($label);
                        $compiler->raw(') ? [] : ["label" => $_label_])');
                    }
                }
            }
        }
        $compiler->raw(')');
    }
}
