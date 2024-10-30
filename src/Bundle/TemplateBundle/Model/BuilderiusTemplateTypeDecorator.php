<?php

namespace Builderius\Bundle\TemplateBundle\Model;

class BuilderiusTemplateTypeDecorator implements BuilderiusTemplateTypeInterface
{
    /**
     * @var BuilderiusTemplateTypeInterface
     */
    private $templateType;

    /**
     * @param BuilderiusTemplateTypeInterface $templateType
     */
    public function __construct(BuilderiusTemplateTypeInterface $templateType)
    {
        $this->templateType = $templateType;
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->templateType->setName($name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->templateType->getName();
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->templateType->setLabel($label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->templateType->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function getTechnologies()
    {
        return $this->templateType->getTechnologies();
    }

    /**
     * @inheritDoc
     */
    public function getTechnology($name)
    {
        return $this->templateType->getTechnology($name);
    }

    /**
     * @inheritDoc
     */
    public function hasTechnology($name)
    {
        return $this->templateType->hasTechnology($name);
    }

    /**
     * @inheritDoc
     */
    public function setTechnologies(array $technologies)
    {
        $this->templateType->setTechnologies($technologies);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTechnology(BuilderiusTemplateTechnologyInterface $technology)
    {
        $this->templateType->addTechnology($technology);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setStandalone($standalone)
    {
        $this->templateType->setStandalone($standalone);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isStandalone()
    {
        return $this->templateType->isStandalone();
    }
}