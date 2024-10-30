<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Proxy;

/**
 * Virtual Proxy - a lazy initializing object wrapping around the proxied subject
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
interface VirtualProxyInterface extends \Builderius\ProxyManager\Proxy\LazyLoadingInterface, \Builderius\ProxyManager\Proxy\ValueHolderInterface
{
}
