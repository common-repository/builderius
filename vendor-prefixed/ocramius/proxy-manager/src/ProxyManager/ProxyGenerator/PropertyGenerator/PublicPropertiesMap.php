<?php

declare (strict_types=1);
namespace Builderius\ProxyManager\ProxyGenerator\PropertyGenerator;

use Builderius\ProxyManager\Generator\Util\IdentifierSuffixer;
use Builderius\ProxyManager\ProxyGenerator\Util\Properties;
use Builderius\Zend\Code\Generator\PropertyGenerator;
/**
 * Map of public properties that exist in the class being proxied
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class PublicPropertiesMap extends \Builderius\Zend\Code\Generator\PropertyGenerator
{
    /**
     * @var bool[]
     */
    private $publicProperties = [];
    /**
     * @param Properties $properties
     *
     * @throws \Builderius\Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(\Builderius\ProxyManager\ProxyGenerator\Util\Properties $properties)
    {
        parent::__construct(\Builderius\ProxyManager\Generator\Util\IdentifierSuffixer::getIdentifier('publicProperties'));
        foreach ($properties->getPublicProperties() as $publicProperty) {
            $this->publicProperties[$publicProperty->getName()] = \true;
        }
        $this->setDefaultValue($this->publicProperties);
        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setStatic(\true);
        $this->setDocBlock('@var bool[] map of public properties of the parent class');
    }
    public function isEmpty() : bool
    {
        return !$this->publicProperties;
    }
}
