<?php

namespace Builderius\Bundle\TemplateBundle\Model;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetAwareTrait;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class BuilderiusTemplateTechnology extends ParameterBag implements BuilderiusTemplateTechnologyInterface
{
    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';

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
}