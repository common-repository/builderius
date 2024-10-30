<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Proxy;

/**
 * Value holder marker
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
interface ValueHolderInterface extends \Builderius\ProxyManager\Proxy\ProxyInterface
{
    /**
     * @return object|null the wrapped value
     */
    public function getWrappedValueHolderValue();
}
