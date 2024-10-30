<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator;

use Builderius\ProxyManager\Generator\Util\IdentifierSuffixer;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Property that contains the wrapped value of a lazy loading proxy
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class ValueHolderProperty extends \Builderius\Zend\Code\Generator\PropertyGenerator
{
    /**
     * Constructor
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct()
    {
        parent::__construct(\Builderius\ProxyManager\Generator\Util\IdentifierSuffixer::getIdentifier('valueHolder'));
        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setDocBlock('@var \\Closure|null initializer responsible for generating the wrapped object');
    }
}
