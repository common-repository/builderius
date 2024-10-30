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

use Builderius\Symfony\Component\HttpFoundation\Request;
use Builderius\Symfony\Component\HttpFoundation\UrlHelper;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFunction;
/**
 * Twig extension for the Symfony HttpFoundation component.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class HttpFoundationExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    private $urlHelper;
    public function __construct(\Builderius\Symfony\Component\HttpFoundation\UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return [new \Builderius\Twig\TwigFunction('absolute_url', [$this, 'generateAbsoluteUrl']), new \Builderius\Twig\TwigFunction('relative_path', [$this, 'generateRelativePath'])];
    }
    /**
     * Returns the absolute URL for the given absolute or relative path.
     *
     * This method returns the path unchanged if no request is available.
     *
     * @see Request::getUriForPath()
     */
    public function generateAbsoluteUrl(string $path) : string
    {
        return $this->urlHelper->getAbsoluteUrl($path);
    }
    /**
     * Returns a relative path based on the current Request.
     *
     * This method returns the path unchanged if no request is available.
     *
     * @see Request::getRelativeUriForPath()
     */
    public function generateRelativePath(string $path) : string
    {
        return $this->urlHelper->getRelativePath($path);
    }
}
