<?php

namespace Builderius\Bundle\TemplateBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusTemplateType extends ParameterBag implements BuilderiusTemplateTypeInterface
{
    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const TECHNOLOGIES_FIELD = 'technologies';
    const STANDALONE_FIELD = 'standalone';

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->set(self::NAME_FIELD, $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->get(self::NAME_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->set(self::LABEL_FIELD, $label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->get(self::LABEL_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function getTechnologies()
    {
        return $this->get(self::TECHNOLOGIES_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function getTechnology($name)
    {
        if ($this->hasTechnology($name)) {
            return $this->getTechnologies()[$name];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasTechnology($name)
    {
        return isset($this->getTechnologies()[$name]);
    }

    /**
     * @inheritDoc
     */
    public function setTechnologies(array $technologies)
    {
        foreach ($technologies as $technology) {
            $this->addTechnology($technology);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTechnology(BuilderiusTemplateTechnologyInterface $technology)
    {
        $technologies = $this->getTechnologies();
        if (!in_array($technology, $technologies)) {
            $technologies[$technology->getName()] = $technology;
            $this->set(self::TECHNOLOGIES_FIELD, $technologies);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isStandalone()
    {
        return $this->get(self::STANDALONE_FIELD, true);
    }

    /**
     * @inheritDoc
     */
    public function setStandalone($standalone)
    {
        $this->set(self::STANDALONE_FIELD, $standalone);

        return $this;
    }
}