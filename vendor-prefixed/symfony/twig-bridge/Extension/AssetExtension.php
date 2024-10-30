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

use Builderius\Symfony\Component\Asset\Packages;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFunction;
/**
 * Twig extension for the Symfony Asset component.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class AssetExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    private $packages;
    public function __construct(\Builderius\Symfony\Component\Asset\Packages $packages)
    {
        $this->packages = $packages;
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return [new \Builderius\Twig\TwigFunction('asset', [$this, 'getAssetUrl']), new \Builderius\Twig\TwigFunction('asset_version', [$this, 'getAssetVersion'])];
    }
    /**
     * Returns the public url/path of an asset.
     *
     * If the package used to generate the path is an instance of
     * UrlPackage, you will always get a URL and not a path.
     */
    public function getAssetUrl(string $path, string $packageName = null) : string
    {
        return $this->packages->getUrl($path, $packageName);
    }
    /**
     * Returns the version of an asset.
     */
    public function getAssetVersion(string $path, string $packageName = null) : string
    {
        return $this->packages->getVersion($path, $packageName);
    }
}
