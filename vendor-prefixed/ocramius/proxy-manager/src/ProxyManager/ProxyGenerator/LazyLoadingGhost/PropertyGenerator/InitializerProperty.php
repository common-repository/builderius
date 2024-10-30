<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator;

use Builderius\ProxyManager\Generator\Util\IdentifierSuffixer;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Property that contains the initializer for a lazy object
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class InitializerProperty extends \Builderius\Zend\Code\Generator\PropertyGenerator
{
    /**
     * Constructor
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct()
    {
        parent::__construct(\Builderius\ProxyManager\Generator\Util\IdentifierSuffixer::getIdentifier('initializer'));
        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setDocBlock('@var \\Closure|null initializer responsible for generating the wrapped object');
    }
}
