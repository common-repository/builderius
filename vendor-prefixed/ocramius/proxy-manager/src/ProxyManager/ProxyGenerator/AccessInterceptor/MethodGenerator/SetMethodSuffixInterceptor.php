<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator;

use Closure;
use Builderius\ProxyManager\Generator\MethodGenerator;
use Builderius\Zend\Code\Generator\ParameterGenerator;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Implementation for {@see \Builderius\ProxyManager\Proxy\AccessInterceptorInterface::setMethodSuffixInterceptor}
 * for access interceptor objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class SetMethodSuffixInterceptor extends \Builderius\ProxyManager\Generator\MethodGenerator
{
    /**
     * Constructor
     *
     * @param PropertyGenerator $suffixInterceptor
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(\Builderius\Zend\Code\Generator\PropertyGenerator $suffixInterceptor)
    {
        parent::__construct('setMethodSuffixInterceptor');
        $interceptor = new \Builderius\Zend\Code\Generator\ParameterGenerator('suffixInterceptor');
        $interceptor->setType(\Closure::class);
        $interceptor->setDefaultValue(null);
        $this->setParameter(new \Builderius\Zend\Code\Generator\ParameterGenerator('methodName', 'string'));
        $this->setParameter($interceptor);
        $this->setBody('$this->' . $suffixInterceptor->getName() . '[$methodName] = $suffixInterceptor;');
    }
}
