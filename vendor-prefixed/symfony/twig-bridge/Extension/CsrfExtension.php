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

use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFunction;
/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
final class CsrfExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return [new \Builderius\Twig\TwigFunction('csrf_token', [\Builderius\Symfony\Bridge\Twig\Extension\CsrfRuntime::class, 'getCsrfToken'])];
    }
}
