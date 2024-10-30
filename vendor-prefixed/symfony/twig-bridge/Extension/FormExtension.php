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

use Builderius\Symfony\Bridge\Twig\TokenParser\FormThemeTokenParser;
use Builderius\Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Builderius\Symfony\Component\Form\FormView;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFilter;
use Builderius\Twig\TwigFunction;
use Builderius\Twig\TwigTest;
/**
 * FormExtension extends Twig with form capabilities.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class FormExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getTokenParsers() : array
    {
        return [
            // {% form_theme form "SomeBundle::widgets.twig" %}
            new \Builderius\Symfony\Bridge\Twig\TokenParser\FormThemeTokenParser(),
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return [new \Builderius\Twig\TwigFunction('form_widget', null, ['node_class' => 'Builderius\\Symfony\\Bridge\\Twig\\Node\\SearchAndRenderBlockNode', 'is_safe' => ['html']]), new \Builderius\Twig\TwigFunction('form_errors', null, ['node_class' => 'Builderius\\Symfony\\Bridge\\Twig\\Node\\SearchAndRenderBlockNode', 'is_safe' => ['html']]), new \Builderius\Twig\TwigFunction('form_label', null, ['node_class' => 'Builderius\\Symfony\\Bridge\\Twig\\Node\\SearchAndRenderBlockNode', 'is_safe' => ['html']]), new \Builderius\Twig\TwigFunction('form_help', null, ['node_class' => 'Builderius\\Symfony\\Bridge\\Twig\\Node\\SearchAndRenderBlockNode', 'is_safe' => ['html']]), new \Builderius\Twig\TwigFunction('form_row', null, ['node_class' => 'Builderius\\Symfony\\Bridge\\Twig\\Node\\SearchAndRenderBlockNode', 'is_safe' => ['html']]), new \Builderius\Twig\TwigFunction('form_rest', null, ['node_class' => 'Builderius\\Symfony\\Bridge\\Twig\\Node\\SearchAndRenderBlockNode', 'is_safe' => ['html']]), new \Builderius\Twig\TwigFunction('form', null, ['node_class' => 'Builderius\\Symfony\\Bridge\\Twig\\Node\\RenderBlockNode', 'is_safe' => ['html']]), new \Builderius\Twig\TwigFunction('form_start', null, ['node_class' => 'Builderius\\Symfony\\Bridge\\Twig\\Node\\RenderBlockNode', 'is_safe' => ['html']]), new \Builderius\Twig\TwigFunction('form_end', null, ['node_class' => 'Builderius\\Symfony\\Bridge\\Twig\\Node\\RenderBlockNode', 'is_safe' => ['html']]), new \Builderius\Twig\TwigFunction('csrf_token', ['Builderius\\Symfony\\Component\\Form\\FormRenderer', 'renderCsrfToken']), new \Builderius\Twig\TwigFunction('form_parent', 'Builderius\\Symfony\\Bridge\\Twig\\Extension\\twig_get_form_parent')];
    }
    /**
     * {@inheritdoc}
     */
    public function getFilters() : array
    {
        return [new \Builderius\Twig\TwigFilter('humanize', ['Builderius\\Symfony\\Component\\Form\\FormRenderer', 'humanize']), new \Builderius\Twig\TwigFilter('form_encode_currency', ['Builderius\\Symfony\\Component\\Form\\FormRenderer', 'encodeCurrency'], ['is_safe' => ['html'], 'needs_environment' => \true])];
    }
    /**
     * {@inheritdoc}
     */
    public function getTests() : array
    {
        return [new \Builderius\Twig\TwigTest('selectedchoice', 'Builderius\\Symfony\\Bridge\\Twig\\Extension\\twig_is_selected_choice'), new \Builderius\Twig\TwigTest('rootform', 'Builderius\\Symfony\\Bridge\\Twig\\Extension\\twig_is_root_form')];
    }
}
/**
 * Returns whether a choice is selected for a given form value.
 *
 * This is a function and not callable due to performance reasons.
 *
 * @param string|array $selectedValue The selected value to compare
 *
 * @see ChoiceView::isSelected()
 */
function twig_is_selected_choice(\Builderius\Symfony\Component\Form\ChoiceList\View\ChoiceView $choice, $selectedValue) : bool
{
    if (\is_array($selectedValue)) {
        return \in_array($choice->value, $selectedValue, \true);
    }
    return $choice->value === $selectedValue;
}
/**
 * @internal
 */
function twig_is_root_form(\Builderius\Symfony\Component\Form\FormView $formView) : bool
{
    return null === $formView->parent;
}
/**
 * @internal
 */
function twig_get_form_parent(\Builderius\Symfony\Component\Form\FormView $formView) : ?\Builderius\Symfony\Component\Form\FormView
{
    return $formView->parent;
}
