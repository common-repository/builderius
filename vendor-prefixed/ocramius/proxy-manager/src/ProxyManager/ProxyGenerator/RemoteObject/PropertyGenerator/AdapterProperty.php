<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\RemoteObject\PropertyGenerator;

use Builderius\ProxyManager\Factory\RemoteObject\AdapterInterface;
use Builderius\ProxyManager\Generator\Util\IdentifierSuffixer;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Property that contains the remote object adapter
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class AdapterProperty extends \Builderius\Zend\Code\Generator\PropertyGenerator
{
    /**
     * Constructor
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct()
    {
        parent::__construct(\Builderius\ProxyManager\Generator\Util\IdentifierSuffixer::getIdentifier('adapter'));
        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setDocBlock('@var \\' . \Builderius\ProxyManager\Factory\RemoteObject\AdapterInterface::class . ' Remote web service adapter');
    }
}
