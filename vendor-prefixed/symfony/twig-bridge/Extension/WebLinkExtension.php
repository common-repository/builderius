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

use Builderius\Symfony\Component\HttpFoundation\RequestStack;
use Builderius\Symfony\Component\WebLink\GenericLinkProvider;
use Builderius\Symfony\Component\WebLink\Link;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFunction;
/**
 * Twig extension for the Symfony WebLink component.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class WebLinkExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    private $requestStack;
    public function __construct(\Builderius\Symfony\Component\HttpFoundation\RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return [new \Builderius\Twig\TwigFunction('link', [$this, 'link']), new \Builderius\Twig\TwigFunction('preload', [$this, 'preload']), new \Builderius\Twig\TwigFunction('dns_prefetch', [$this, 'dnsPrefetch']), new \Builderius\Twig\TwigFunction('preconnect', [$this, 'preconnect']), new \Builderius\Twig\TwigFunction('prefetch', [$this, 'prefetch']), new \Builderius\Twig\TwigFunction('prerender', [$this, 'prerender'])];
    }
    /**
     * Adds a "Link" HTTP header.
     *
     * @param string $rel        The relation type (e.g. "preload", "prefetch", "prerender" or "dns-prefetch")
     * @param array  $attributes The attributes of this link (e.g. "['as' => true]", "['pr' => 0.5]")
     *
     * @return string The relation URI
     */
    public function link(string $uri, string $rel, array $attributes = []) : string
    {
        if (!($request = $this->requestStack->getMasterRequest())) {
            return $uri;
        }
        $link = new \Builderius\Symfony\Component\WebLink\Link($rel, $uri);
        foreach ($attributes as $key => $value) {
            $link = $link->withAttribute($key, $value);
        }
        $linkProvider = $request->attributes->get('_links', new \Builderius\Symfony\Component\WebLink\GenericLinkProvider());
        $request->attributes->set('_links', $linkProvider->withLink($link));
        return $uri;
    }
    /**
     * Preloads a resource.
     *
     * @param array $attributes The attributes of this link (e.g. "['as' => true]", "['crossorigin' => 'use-credentials']")
     *
     * @return string The path of the asset
     */
    public function preload(string $uri, array $attributes = []) : string
    {
        return $this->link($uri, 'preload', $attributes);
    }
    /**
     * Resolves a resource origin as early as possible.
     *
     * @param array $attributes The attributes of this link (e.g. "['as' => true]", "['pr' => 0.5]")
     *
     * @return string The path of the asset
     */
    public function dnsPrefetch(string $uri, array $attributes = []) : string
    {
        return $this->link($uri, 'dns-prefetch', $attributes);
    }
    /**
     * Initiates a early connection to a resource (DNS resolution, TCP handshake, TLS negotiation).
     *
     * @param array $attributes The attributes of this link (e.g. "['as' => true]", "['pr' => 0.5]")
     *
     * @return string The path of the asset
     */
    public function preconnect(string $uri, array $attributes = []) : string
    {
        return $this->link($uri, 'preconnect', $attributes);
    }
    /**
     * Indicates to the client that it should prefetch this resource.
     *
     * @param array $attributes The attributes of this link (e.g. "['as' => true]", "['pr' => 0.5]")
     *
     * @return string The path of the asset
     */
    public function prefetch(string $uri, array $attributes = []) : string
    {
        return $this->link($uri, 'prefetch', $attributes);
    }
    /**
     * Indicates to the client that it should prerender this resource .
     *
     * @param array $attributes The attributes of this link (e.g. "['as' => true]", "['pr' => 0.5]")
     *
     * @return string The path of the asset
     */
    public function prerender(string $uri, array $attributes = []) : string
    {
        return $this->link($uri, 'prerender', $attributes);
    }
}
