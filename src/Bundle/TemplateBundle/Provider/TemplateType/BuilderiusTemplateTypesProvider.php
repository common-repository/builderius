<?php

namespace Builderius\Bundle\TemplateBundle\Provider\TemplateType;

use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateTechnologyInterface;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateTypeInterface;

class BuilderiusTemplateTypesProvider implements BuilderiusTemplateTypesProviderInterface
{
    /**
     * @var BuilderiusTemplateTypeInterface[]
     */
    private $templateTypes = [];

    /**
     * @var BuilderiusTemplateTechnologyInterface[]
     */
    private $templateTechnologies = [];

    /**
     * @var array
     */
    private $templateTypesByTechnologies = [];

    /**
     * @param BuilderiusTemplateTypeInterface $type
     * @return $this
     */
    public function addType(BuilderiusTemplateTypeInterface $type)
    {
        if (!in_array($type, $this->templateTypes)) {
            $this->templateTypes[$type->getName()] = $type;
        }
        foreach ($type->getTechnologies() as $technology) {
            $this->templateTechnologies[$technology->getName()] = $technology;
            $this->templateTypesByTechnologies[$technology->getName()][$type->getName()] = $type;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTypes()
    {
        return $this->templateTypes;
    }

    /**
     * @inheritDoc
     */
    public function getType($name)
    {
        if ($this->hasType($name)) {
            return $this->templateTypes[$name];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasType($name)
    {
        return isset($this->templateTypes[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getTypesWithTechnology($technology)
    {
        if (isset($this->templateTypesByTechnologies[$technology])) {
            return $this->templateTypesByTechnologies[$technology];
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function getTechnologies()
    {
        return $this->templateTechnologies;
    }
}