<?php

namespace Builderius\Bundle\TemplateBundle\DynamicDataHelper;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;

class DynamicDataHelpersCategory extends ParameterBag implements DynamicDataHelpersCategoryInterface
{
    const NAME_FIELD = 'name';
    const LABEL_FIELD = 'label';
    const SORT_ORDER_FIELD = 'sortOrder';

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
    public function setName($name)
    {
        $this->set(self::NAME_FIELD, $name);

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
    public function setLabel($label)
    {
        $this->set(self::LABEL_FIELD, $label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->get(self::SORT_ORDER_FIELD, 10);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($sortOrder)
    {
        $this->set(self::SORT_ORDER_FIELD, $sortOrder);

        return $this;
    }
}