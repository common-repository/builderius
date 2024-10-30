<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator;

use Builderius\ProxyManager\Factory\RemoteObject\AdapterInterface;
use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\ProxyManager\ProxyGenerator\Util\Properties;
use Builderius\ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator;
use ReflectionClass;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * The `staticProxyConstructor` implementation for remote object proxies
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class StaticProxyConstructor extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * Constructor
     *
     * @param ReflectionClass   $originalClass Reflection of the class to proxy
     * @param PropertyGenerator $adapter       Adapter property
     */
    public function __construct(\ReflectionClass $originalClass, \Builderius\Zend\Code\Generator\PropertyGenerator $adapter)
    {
        $adapterName = $adapter->getName();
        parent::__construct('staticProxyConstructor', [new \Builderius\Zend\Code\Generator\ParameterGenerator($adapterName, \Builderius\ProxyManager\Factory\RemoteObject\AdapterInterface::class)], \Builderius\ProxyManager\Generator\MethodGenerator::FLAG_PUBLIC | \Builderius\ProxyManager\Generator\MethodGenerator::FLAG_STATIC, null, 'Constructor for remote object control\\n\\n' . '@param \\ProxyManager\\Factory\\RemoteObject\\AdapterInterface \\$adapter');
        $body = 'static $reflection;' . "\n\n" . '$reflection = $reflection ?? $reflection = new \\ReflectionClass(__CLASS__);' . "\n" . '$instance = $reflection->newInstanceWithoutConstructor();' . "\n\n" . '$instance->' . $adapterName . ' = $' . $adapterName . ";\n\n" . \Builderius\ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator::generateSnippet(\Builderius\ProxyManager\ProxyGenerator\Util\Properties::fromReflectionClass($originalClass), 'instance');
        $this->setBody($body . "\n\nreturn \$instance;");
    }
}
