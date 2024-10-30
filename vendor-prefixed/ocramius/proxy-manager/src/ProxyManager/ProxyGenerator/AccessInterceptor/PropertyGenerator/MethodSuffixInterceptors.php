<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator;

use Builderius\ProxyManager\Generator\Util\IdentifierSuffixer;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Property that contains the interceptor for operations to be executed after method execution
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MethodSuffixInterceptors extends \Builderius\Zend\Code\Generator\PropertyGenerator
{
    /**
     * Constructor
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct()
    {
        parent::__construct(\Builderius\ProxyManager\Generator\Util\IdentifierSuffixer::getIdentifier('methodSuffixInterceptors'));
        $this->setDefaultValue([]);
        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setDocBlock('@var \\Closure[] map of interceptors to be called per-method after execution');
    }
}
