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

use Builderius\Symfony\Component\HttpKernel\Controller\ControllerReference;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFunction;
/**
 * Provides integration with the HttpKernel component.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class HttpKernelExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return [new \Builderius\Twig\TwigFunction('render', [\Builderius\Symfony\Bridge\Twig\Extension\HttpKernelRuntime::class, 'renderFragment'], ['is_safe' => ['html']]), new \Builderius\Twig\TwigFunction('render_*', [\Builderius\Symfony\Bridge\Twig\Extension\HttpKernelRuntime::class, 'renderFragmentStrategy'], ['is_safe' => ['html']]), new \Builderius\Twig\TwigFunction('controller', static::class . '::controller')];
    }
    public static function controller(string $controller, array $attributes = [], array $query = []) : \Builderius\Symfony\Component\HttpKernel\Controller\ControllerReference
    {
        return new \Builderius\Symfony\Component\HttpKernel\Controller\ControllerReference($controller, $attributes, $query);
    }
}
