<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Factory\RemoteObject\Adapter;

/**
 * Remote Object JSON RPC adapter
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class JsonRpc extends \Builderius\ProxyManager\Factory\RemoteObject\Adapter\BaseAdapter
{
    /**
     * {@inheritDoc}
     */
    protected function getServiceName(string $wrappedClass, string $method) : string
    {
        return $wrappedClass . '.' . $method;
    }
}
