<?php

namespace Builderius\Bundle\TemplateBundle\Model;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusTemplateSubType extends ParameterBag implements BuilderiusTemplateSubTypeInterface
{
    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const TYPE_FIELD = 'type';
    const THEME_DISABLED_FIELD = 'theme_disabled';

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
    public function setType($type)
    {
        $this->set(self::TYPE_FIELD, $type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->get(self::TYPE_FIELD);
    }

    /**
     * @inheritDoc
     */
    public function isThemeDisabled()
    {
        return $this->get(self::THEME_DISABLED_FIELD, true);
    }

    /**
     * @inheritDoc
     */
    public function setThemeDisabled($themeDisabled)
    {
        $this->set(self::THEME_DISABLED_FIELD, $themeDisabled);

        return $this;
    }
}