<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator;

use Closure;
use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Implementation for {@see \Builderius\ProxyManager\Proxy\AccessInterceptorInterface::setMethodPrefixInterceptor}
 * for access interceptor objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class SetMethodPrefixInterceptor extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * Constructor
     *
     * @param PropertyGenerator $prefixInterceptor
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(\Builderius\Zend\Code\Generator\PropertyGenerator $prefixInterceptor)
    {
        parent::__construct('setMethodPrefixInterceptor');
        $interceptor = new \Builderius\Zend\Code\Generator\ParameterGenerator('prefixInterceptor');
        $interceptor->setType(\Closure::class);
        $interceptor->setDefaultValue(null);
        $this->setParameter(new \Builderius\Zend\Code\Generator\ParameterGenerator('methodName', 'string'));
        $this->setParameter($interceptor);
        $this->setBody('$this->' . $prefixInterceptor->getName() . '[$methodName] = $prefixInterceptor;');
    }
}
