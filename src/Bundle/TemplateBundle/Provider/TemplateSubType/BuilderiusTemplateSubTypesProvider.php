<?php

namespace Builderius\Bundle\TemplateBundle\Provider\TemplateSubType;

use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateSubTypeInterface;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateTypeInterface;

class BuilderiusTemplateSubTypesProvider implements BuilderiusTemplateSubTypesProviderInterface
{
    /**
     * @var BuilderiusTemplateSubTypeInterface[]
     */
    private $templateSubTypes = [];

    /**
     * @param BuilderiusTemplateTypeInterface $type
     * @return $this
     */
    public function addSubType(BuilderiusTemplateSubTypeInterface $subType)
    {
        if (!in_array($subType, $this->templateSubTypes)) {
            $this->templateSubTypes[$subType->getType()][$subType->getName()] = $subType;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubTypes($type)
    {
        if (isset($this->templateSubTypes[$type])) {
            return $this->templateSubTypes[$type];
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function getSubType($type, $name)
    {
        if ($this->hasType($type, $name)) {
            return $this->templateSubTypes[$type][$name];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasSubType($type, $name)
    {
        if (!isset($this->templateSubTypes[$type])) {
            return false;
        }
        if (!isset($this->templateSubTypes[$type][$name])) {
            return false;
        }

        return $this->templateSubTypes[$type][$name];
    }
}