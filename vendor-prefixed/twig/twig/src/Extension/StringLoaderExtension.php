<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Twig\Extension;

use Builderius\Twig\TwigFunction;
final class StringLoaderExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    public function getFunctions() : array
    {
        return [new \Builderius\Twig\TwigFunction('template_from_string', 'twig_template_from_string', ['needs_environment' => \true])];
    }
}
namespace Builderius;

use Builderius\Twig\Environment;
use Builderius\Twig\TemplateWrapper;
/**
 * Loads a template from a string.
 *
 *     {{ include(template_from_string("Hello {{ name }}")) }}
 *
 * @param string $template A template as a string or object implementing __toString()
 * @param string $name     An optional name of the template to be used in error messages
 */
function twig_template_from_string(\Builderius\Twig\Environment $env, $template, string $name = null) : \Builderius\Twig\TemplateWrapper
{
    return $env->createTemplate((string) $template, $name);
}
