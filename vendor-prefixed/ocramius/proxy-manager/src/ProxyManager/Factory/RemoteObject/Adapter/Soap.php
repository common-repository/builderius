<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\Factory\RemoteObject\Adapter;

/**
 * Remote Object SOAP adapter
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class Soap extends \Builderius\ProxyManager\Factory\RemoteObject\Adapter\BaseAdapter
{
    /**
     * {@inheritDoc}
     */
    protected function getServiceName(string $wrappedClass, string $method) : string
    {
        return $method;
    }
}
